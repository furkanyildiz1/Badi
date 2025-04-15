<?php
class BunnyStream {
    private $apiKey;
    private $libraryId;
    private $hostname;
    
    public function __construct($apiKey, $libraryId, $hostname) {
        $this->apiKey = $apiKey;
        $this->libraryId = $libraryId;
        $this->hostname = $hostname;
    }
    
    /**
     * Upload a video file to Bunny Stream within a course collection
     * 
     * @param string $filePath Path to the temporary file
     * @param string $title Title for the video
     * @param string $courseName Course name for the collection
     * @return array|false Returns video data on success, false on failure
     */
    public function uploadToCourseCollection($filePath, $title, $courseName) {
        // Step 1: Create or get course collection
        $courseCollection = $this->getOrCreateCollection($courseName);
        if (!$courseCollection) {
            error_log("Failed to create/get course collection: $courseName");
            return false;
        }
        
        // Step 2: Create video in the course collection
        $createData = [
            'title' => $title,
            'collectionId' => $courseCollection['guid']
        ];
        
        $videoData = $this->createVideo($createData);
        
        if (!$videoData || !isset($videoData['guid'])) {
            error_log("Failed to create video in Bunny Stream: " . json_encode($videoData));
            return false;
        }
        
        $guid = $videoData['guid'];
        
        // Step 3: Upload the video file
        $uploadSuccess = $this->uploadVideoFile($guid, $filePath);
        
        if (!$uploadSuccess) {
            // Clean up by deleting the video entry if upload failed
            $this->deleteVideo($guid);
            return false;
        }
        
        return $videoData;
    }
    
    /**
     * Get a collection by name or create it if it doesn't exist
     * 
     * @param string $name Collection name
     * @param string $parentCollectionId Parent collection GUID (optional)
     * @return array|false Collection data or false on failure
     */
    public function getOrCreateCollection($name, $parentCollectionId = null) {
        // First try to find existing collection
        $collections = $this->listCollections($parentCollectionId);
        
        if ($collections) {
            foreach ($collections as $collection) {
                // Check if collection matches our criteria
                if ($collection['name'] === $name) {
                    // If parent is specified, check if this collection has that parent
                    if ($parentCollectionId) {
                        if (isset($collection['parentGuid']) && 
                            $collection['parentGuid'] === $parentCollectionId) {
                            return $collection;
                        }
                    } else {
                        // For top-level collections, it should have no parent
                        if (empty($collection['parentGuid'])) {
                            return $collection;
                        }
                    }
                }
            }
        }
        
        // Collection not found, create it
        return $this->createCollection($name, $parentCollectionId);
    }
    
    /**
     * List all collections in the library
     * 
     * @param string $parentGuid Optional parent GUID to filter by
     * @return array|false Collections or false on failure
     */
    public function listCollections($parentGuid = null) {
        $url = "https://video.bunnycdn.com/library/{$this->libraryId}/collections";
        
        // If parent GUID is provided, add it as a query parameter
        if ($parentGuid) {
            $url .= "?parentGuid=" . urlencode($parentGuid);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            error_log("Collections retrieved" . ($parentGuid ? " for parent {$parentGuid}" : "") . ": " . $response);
            return $data['items'] ?? [];
        }
        
        error_log("List collections error: " . $response);
        return false;
    }
    
    /**
     * Create a new collection
     * 
     * @param string $name Collection name
     * @param string $parentCollectionId Parent collection GUID (optional)
     * @return array|false Collection data or false on failure
     */
    public function createCollection($name, $parentCollectionId = null) {
        // Create request data object
        $data = [
            "name" => $name
        ];
        
        if ($parentCollectionId) {
            // Make sure we're using the exact field name the API expects
            $data["parentGuid"] = $parentCollectionId;
            error_log("Creating collection '{$name}' with parent GUID: {$parentCollectionId}");
            
            // Also log the complete request payload for debugging
            error_log("Collection creation payload: " . json_encode($data));
        } else {
            error_log("Creating top-level collection '{$name}'");
        }
        
        $ch = curl_init();
        $url = "https://video.bunnycdn.com/library/{$this->libraryId}/collections";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        
        // Convert to JSON with JSON_UNESCAPED_UNICODE to handle non-ASCII characters properly
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        
        // Add verbose error reporting
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Get verbose information
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("CURL Verbose: " . $verboseLog);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $responseData = json_decode($response, true);
            error_log("Collection created successfully: " . $response);
            
            // After creation, verify the collection has the correct parent
            $verifyCollection = $this->getCollection($responseData['guid']);
            if ($verifyCollection) {
                error_log("Verified collection: " . json_encode($verifyCollection));
            }
            
            return $responseData;
        }
        
        error_log("Create collection error (HTTP {$httpCode}): " . $response);
        return false;
    }
    
    /**
     * Get a specific collection by GUID
     * 
     * @param string $guid Collection GUID
     * @return array|false Collection data or false on failure
     */
    public function getCollection($guid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$this->libraryId}/collections/{$guid}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        error_log("Get collection error: " . $response);
        return false;
    }
    
    /**
     * Create a video entry in Bunny Stream
     * 
     * @param array $data Video metadata
     * @return array|false Returns response data on success, false on failure
     */
    private function createVideo($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$this->libraryId}/videos");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        error_log("Create video error: " . $response);
        return false;
    }
    
    /**
     * Upload a video file to an existing Bunny Stream video
     * 
     * @param string $guid Video GUID
     * @param string $filePath Path to the video file
     * @return bool True on success, false on failure
     */
    private function uploadVideoFile($guid, $filePath) {
        if (!file_exists($filePath)) {
            error_log("File does not exist: $filePath");
            return false;
        }
        
        // Get file size
        $fileSize = filesize($filePath);
        
        // Open file handle
        $fh = fopen($filePath, 'r');
        if (!$fh) {
            error_log("Failed to open file: $filePath");
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$guid}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close file handle
        fclose($fh);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }
        
        error_log("Upload video error: " . $response);
        return false;
    }
    
    /**
     * Delete a video from Bunny Stream
     * 
     * @param string $guid Video GUID to delete
     * @return bool True on success, false on failure
     */
    public function deleteVideo($guid) {
        if (empty($guid)) {
            error_log("Cannot delete video: Empty GUID");
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$guid}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Curl error when deleting video: $error");
            return false;
        }
        
        // 204 No Content is the expected response for successful deletion
        if ($httpCode >= 200 && $httpCode < 300) {
            error_log("Successfully deleted video from Bunny Stream: $guid");
            return true;
        }
        
        error_log("Failed to delete video from Bunny Stream: $guid, HTTP Code: $httpCode, Response: $response");
        return false;
    }
    
    /**
     * Get video details
     * 
     * @param string $guid Video GUID
     * @return array|false Returns video data on success, false on failure
     */
    public function getVideo($guid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$guid}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        return false;
    }
    
    /**
     * Generate a HLS playback URL
     * 
     * @param string $guid Video GUID
     * @return string HLS playback URL
     */
    public function getPlaybackUrl($guid) {
        return "https://{$this->hostname}/{$guid}/playlist.m3u8";
    }
    
    /**
     * List all videos in the library
     * 
     * @param int $page Page number (starts at 1)
     * @param int $perPage Items per page (default 100)
     * @return array|false Returns videos or false on failure
     */
    public function listVideos($page = 1, $perPage = 100) {
        $ch = curl_init();
        $url = "https://video.bunnycdn.com/library/{$this->libraryId}/videos?page={$page}&itemsPerPage={$perPage}&orderBy=date";
        
        error_log("Fetching videos from URL: $url");
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "AccessKey: {$this->apiKey}"
        ]);
        
        // Add timeout to prevent hanging
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        
        curl_close($ch);
        
        // Log the response for debugging
        error_log("Bunny API response code: $httpCode");
        
        if ($error) {
            error_log("Curl error ($errno): $error");
            return false;
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            if (empty($response)) {
                error_log("Empty response from Bunny API");
                return [];
            }
            
            // Log first part of response for debugging
            error_log("Bunny API response (first 300 chars): " . substr($response, 0, 300));
            
            try {
                $data = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON decode error: " . json_last_error_msg());
                    error_log("Raw response: " . $response);
                    return false;
                }
                
                return isset($data['items']) ? $data['items'] : [];
            } catch (Exception $e) {
                error_log("Exception when decoding JSON: " . $e->getMessage());
                return false;
            }
        }
        
        error_log("List videos error: HTTP $httpCode, Response: " . $response);
        return false;
    }
    
    /**
     * Generate an embed code for a video
     * 
     * @param string $guid Video GUID
     * @param int $width Width in pixels (default 100%)
     * @param int $height Height in pixels (default 400)
     * @return string HTML embed code
     */
    public function getEmbedCode($guid, $width = '100%', $height = 400) {
        return '<div style="position:relative;padding-top:56.25%;"><iframe src="https://iframe.mediadelivery.net/embed/' . $this->libraryId . '/' . $guid . '?autoplay=false&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div>';
    }
}
?> 
