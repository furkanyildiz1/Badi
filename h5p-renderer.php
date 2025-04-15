<?php
// Simple H5P content renderer without relying on H5P libraries
header('Content-Type: text/html; charset=utf-8');

// Get the H5P file path
$h5p_path = isset($_GET['h5p']) ? $_GET['h5p'] : '';

if (empty($h5p_path) || !file_exists($h5p_path)) {
    echo '<div style="color: red; padding: 20px;">H5P file not found: ' . htmlspecialchars($h5p_path) . '</div>';
    exit;
}

// Create extraction directory
$extract_dir = 'uploads/h5p-extracted/' . pathinfo($h5p_path, PATHINFO_FILENAME);
if (!is_dir($extract_dir)) {
    mkdir($extract_dir, 0755, true);
    
    // Extract H5P package
    $zip = new ZipArchive;
    if ($zip->open($h5p_path) === TRUE) {
        $zip->extractTo($extract_dir);
        $zip->close();
    } else {
        echo '<div style="color: red; padding: 20px;">Failed to extract H5P package.</div>';
        exit;
    }
}

// Check for content.json
$content_json_path = $extract_dir . '/content/content.json';
$h5p_json_path = $extract_dir . '/h5p.json';

if (!file_exists($content_json_path) || !file_exists($h5p_json_path)) {
    echo '<div style="color: red; padding: 20px;">Invalid H5P package: required files not found.</div>';
    exit;
}

// Load content and metadata
$content_json = file_get_contents($content_json_path);
$content_data = json_decode($content_json, TRUE);
$h5p_json = file_get_contents($h5p_json_path);
$h5p_data = json_decode($h5p_json, TRUE);

// Get title
$title = isset($h5p_data['title']) ? $h5p_data['title'] : 'Untitled H5P Content';

// Get base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

// Find video file in the content
$videoSource = null;
$posterImage = null;

// Function to find videos recursively in the content structure
function findVideoSources($data, $baseDir) {
    $sources = [];
    $poster = null;
    
    // If this is a video definition
    if (isset($data['mime']) && strpos($data['mime'], 'video/') === 0) {
        if (isset($data['path'])) {
            // Check if path is already an absolute URL
            if (preg_match('/^https?:\/\//', $data['path'])) {
                $sources[] = $data['path']; // Use as is
            } else {
                $sources[] = $baseDir . '/' . $data['path']; // Make it absolute
            }
        }
        return [$sources, $poster];
    }
    
    // Check for video sources in standard format
    if (isset($data['sources'])) {
        foreach ($data['sources'] as $source) {
            if (isset($source['path'])) {
                // Check if path is already an absolute URL
                if (preg_match('/^https?:\/\//', $source['path'])) {
                    $sources[] = $source['path']; // Use as is
                } else {
                    $sources[] = $baseDir . '/' . $source['path']; // Make it absolute
                }
            }
        }
        
        // Check for poster
        if (isset($data['posterImagePath'])) {
            // Check if poster path is already an absolute URL
            if (preg_match('/^https?:\/\//', $data['posterImagePath'])) {
                $poster = $data['posterImagePath'];
            } else {
                $poster = $baseDir . '/' . $data['posterImagePath'];
            }
        }
        return [$sources, $poster];
    }
    
    // For Interactive Video specific format
    if (isset($data['video']) && isset($data['video']['files'])) {
        foreach ($data['video']['files'] as $file) {
            if (isset($file['path'])) {
                // Check if path is already an absolute URL
                if (preg_match('/^https?:\/\//', $file['path'])) {
                    $sources[] = $file['path']; // Use as is
                } else {
                    $sources[] = $baseDir . '/' . $file['path']; // Make it absolute
                }
            }
        }
        
        // Check for poster in video parameters
        if (isset($data['video']['startScreenOptions']) && isset($data['video']['startScreenOptions']['poster'])) {
            // Check if poster path is already an absolute URL
            if (preg_match('/^https?:\/\//', $data['video']['startScreenOptions']['poster'])) {
                $poster = $data['video']['startScreenOptions']['poster'];
            } else {
                $poster = $baseDir . '/' . $data['video']['startScreenOptions']['poster'];
            }
        }
        return [$sources, $poster];
    }
    
    // Recursively search in properties
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            list($foundSources, $foundPoster) = findVideoSources($value, $baseDir);
            if (!empty($foundSources)) {
                $sources = array_merge($sources, $foundSources);
            }
            if ($foundPoster) {
                $poster = $foundPoster;
            }
        }
    }
    
    return [$sources, $poster];
}

// Search for video in content
list($videoSources, $posterImage) = findVideoSources($content_data, $baseUrl . '/' . $extract_dir . '/content');

// If no video found in standard places, try to find by scanning for video files
if (empty($videoSources)) {
    $contentDir = $extract_dir . '/content';
    $videoFiles = [];
    
    // Function to scan directory recursively for video files
    function scanForVideos($dir, &$results) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                scanForVideos($path, $results);
            } else {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                    $results[] = $path;
                }
            }
        }
    }
    
    scanForVideos($contentDir, $videoFiles);
    
    foreach ($videoFiles as $videoFile) {
        // Check if path is already an absolute URL
        if (preg_match('/^https?:\/\//', $videoFile)) {
            $videoSources[] = $videoFile; // Use as is
        } else {
            $videoSources[] = $baseUrl . '/' . $videoFile; // Make it absolute
        }
    }
}

// If we found sources, use the first one as primary
$primarySource = !empty($videoSources) ? $videoSources[0] : null;

// Render HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($title); ?> - H5P Viewer</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .h5p-container {
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            padding: 20px;
            margin: 0 auto 20px;
            max-width: 900px;
        }
        .h5p-title {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .video-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .no-video {
            padding: 40px;
            background: #f8f8f8;
            text-align: center;
            color: #666;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="h5p-container">
        <h1 class="h5p-title"><?php echo htmlspecialchars($title); ?></h1>
        
        <?php if ($primarySource): ?>
        <div class="video-container">
            <video controls <?php echo $posterImage ? 'poster="' . htmlspecialchars($posterImage) . '"' : ''; ?>>
                <?php foreach ($videoSources as $src): ?>
                <source src="<?php echo htmlspecialchars($src); ?>" type="<?php 
                    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
                    $type = 'video/mp4'; // default
                    if ($ext === 'webm') $type = 'video/webm';
                    if ($ext === 'ogg') $type = 'video/ogg';
                    echo $type;
                ?>">
                <?php endforeach; ?>
                Your browser does not support the video tag.
            </video>
        </div>
        
        <div class="video-info">
            <p><strong>Note:</strong> This is a simplified view of the H5P content. Interactive elements are not available in this mode.</p>
        </div>
        <?php else: ?>
        <div class="no-video">
            <h3>No video content found</h3>
            <p>This H5P package does not contain a recognizable video file, or the video file could not be located.</p>
            <p>Content structure:</p>
            <pre style="text-align: left; overflow: auto; max-height: 300px;"><?php echo htmlspecialchars(json_encode($content_data, JSON_PRETTY_PRINT)); ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>