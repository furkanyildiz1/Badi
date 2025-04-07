<?php
include 'nedmin/netting/baglan.php';
include 'header.php';

// Get the legal texts from database
$legalsor = $db->prepare("SELECT * FROM kvkk_gizlilik WHERE id=:id");
$legalsor->execute([
    'id' => 1
]);
$legal = $legalsor->fetch(PDO::FETCH_ASSOC);
?>

<!-- Start Page Title Area - Updated to match about-us.php style -->
<section class="page-title-area item-bg1">
    <div class="container">
        <div class="page-title-content">
            <h2>KVKK ve Gizlilik</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
                <li class="breadcrumb-item"></li>
                <li class="primery-link">KVKK ve Gizlilik</li>
            </ul>
        </div>
    </div>
</section>
<!-- End Page Title Area -->

<!-- Start Legal Content Area -->
<div class="edu-about-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="sticky-top" style="top: 120px; z-index:1;">
                    <div class="list-group">
                        <a href="#kvkk" class="list-group-item list-group-item-action">KVKK Metni</a>
                        <a href="#gizlilik" class="list-group-item list-group-item-action">Gizlilik Politikası</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <!-- KVKK Section -->
                <section id="kvkk" class="mb-5">
                    <div class="edu-section-title">
                        <h2><span class="shape02">KVKK</span> Metni</h2>
                    </div>
                    <div class="legal-content">
                        <?php echo isset($legal['kvkk_metin']) ? $legal['kvkk_metin'] : ''; ?>
                    </div>
                </section>

                <!-- Privacy Policy Section -->
                <section id="gizlilik" class="mb-5">
                    <div class="edu-section-title">
                        <h2><span class="shape02">Gizlilik</span> Politikası</h2>
                    </div>
                    <div class="legal-content">
                        <?php echo isset($legal['gizlilik_politikasi']) ? $legal['gizlilik_politikasi'] : ''; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<!-- End Legal Content Area -->

<style>
    .legal-content {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }
    
    .legal-content h2, 
    .legal-content h3 {
        color: #333;
        margin-bottom: 20px;
    }
    
    .legal-content p {
        margin-bottom: 15px;
        line-height: 1.7;
    }
    
    .legal-content ul {
        margin-left: 20px;
        margin-bottom: 20px;
    }
    
    .legal-content ul li {
        margin-bottom: 10px;
    }
    
    .list-group-item.active {
        background-color: #26B99A;
        border-color: #26B99A;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                    
                    // Add active class to clicked nav item
                    document.querySelectorAll('.list-group-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });
        
        // Highlight active section on scroll
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY + 150;
            
            document.querySelectorAll('section[id]').forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    const currentId = section.getAttribute('id');
                    document.querySelectorAll('.list-group-item').forEach(item => {
                        item.classList.remove('active');
                        if (item.getAttribute('href') === '#' + currentId) {
                            item.classList.add('active');
                        }
                    });
                }
            });
        });
    });
</script>

<?php include 'footer.php'; ?> 