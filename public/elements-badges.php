<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badges - Bootstrap 5 Elements</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Bootstrap 5 badge examples - small count and labeling components">
    <meta name="keywords" content="bootstrap, badges, labels, counts, notifications, indicators">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./assets/favicon-CvUZKS4z.svg">
    <link rel="icon" type="image/png" href="./assets/favicon-B_cwPWBd.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="./assets/manifest-DTaoG9pG.json">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Prism.js for syntax highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    
    <!-- Custom syntax highlighting overrides -->
    <style>
        .element-code-block pre[class*="language-"] {
            background: #1e1e1e !important;
            border: 1px solid #333 !important;
            border-radius: 0.5rem !important;
            margin: 0 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }
            <?php include '../app/includes/header.php'; ?>

            <?php include '../app/includes/sidebar.php'; ?>

            <!-- Floating Hamburger Menu -->
            <button class="hamburger-menu" data-sidebar-toggle>
                <i class="bi bi-list"></i>
            </button>

            <!-- Main Content -->
            <main class="admin-main">
                <div class="container-fluid p-4">
                    
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/elements">Elements</a></li>
                            <li class="breadcrumb-item active">Badges</li>
                        </ol>
                    </nav>

                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-0">Badges</h1>
                            <p class="text-muted mb-0">Small count and labeling components</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button class="btn btn-primary" onclick="copyAllCode()">
                                <i class="bi bi-clipboard me-2"></i>Copy All
                            </button>
                        </div>
                    </div>

                    <!-- Badge Examples -->
                    <div class="row g-4">
                        
                        <!-- Basic Badges -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Basic Badges</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <span class="badge bg-primary me-2 mb-2">Primary</span>
                                        <span class="badge bg-secondary me-2 mb-2">Secondary</span>
                                        <span class="badge bg-success me-2 mb-2">Success</span>
                                        <span class="badge bg-danger me-2 mb-2">Danger</span>
                                        <span class="badge bg-warning me-2 mb-2">Warning</span>
                                        <span class="badge bg-info me-2 mb-2">Info</span>
                                        <span class="badge bg-light text-dark me-2 mb-2">Light</span>
                                        <span class="badge bg-dark me-2 mb-2">Dark</span>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;span class="badge bg-primary"&gt;Primary&lt;/span&gt;
&lt;span class="badge bg-secondary"&gt;Secondary&lt;/span&gt;
&lt;span class="badge bg-success"&gt;Success&lt;/span&gt;
&lt;span class="badge bg-danger"&gt;Danger&lt;/span&gt;
&lt;span class="badge bg-warning"&gt;Warning&lt;/span&gt;
&lt;span class="badge bg-info"&gt;Info&lt;/span&gt;
&lt;span class="badge bg-light text-dark"&gt;Light&lt;/span&gt;
&lt;span class="badge bg-dark"&gt;Dark&lt;/span&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pill Badges -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Pill Badges</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <span class="badge rounded-pill bg-primary me-2 mb-2">Primary</span>
                                        <span class="badge rounded-pill bg-secondary me-2 mb-2">Secondary</span>
                                        <span class="badge rounded-pill bg-success me-2 mb-2">Success</span>
                                        <span class="badge rounded-pill bg-danger me-2 mb-2">Danger</span>
                                        <span class="badge rounded-pill bg-warning me-2 mb-2">Warning</span>
                                        <span class="badge rounded-pill bg-info me-2 mb-2">Info</span>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;span class="badge rounded-pill bg-primary"&gt;Primary&lt;/span&gt;
&lt;span class="badge rounded-pill bg-secondary"&gt;Secondary&lt;/span&gt;
&lt;span class="badge rounded-pill bg-success"&gt;Success&lt;/span&gt;
&lt;span class="badge rounded-pill bg-danger"&gt;Danger&lt;/span&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Badges with Icons -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Badges with Icons</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <span class="badge bg-primary me-2 mb-2">
                                            <i class="bi bi-star me-1"></i>Featured
                                        </span>
                                        <span class="badge bg-success me-2 mb-2">
                                            <i class="bi bi-check-circle me-1"></i>Verified
                                        </span>
                                        <span class="badge bg-warning me-2 mb-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Warning
                                        </span>
                                        <span class="badge bg-danger me-2 mb-2">
                                            <i class="bi bi-x-circle me-1"></i>Error
                                        </span>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;span class="badge bg-primary"&gt;
    &lt;i class="bi bi-star me-1"&gt;&lt;/i&gt;Featured
&lt;/span&gt;
&lt;span class="badge bg-success"&gt;
    &lt;i class="bi bi-check-circle me-1"&gt;&lt;/i&gt;Verified
&lt;/span&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Positioned Badges -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Positioned Badges</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <button type="button" class="btn btn-primary position-relative me-3 mb-3">
                                            Inbox
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                99+
                                            </span>
                                        </button>
                                        <button type="button" class="btn btn-secondary position-relative me-3 mb-3">
                                            Profile
                                            <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </button>
                                        <div class="position-relative d-inline-block me-3">
                                            <i class="bi bi-bell fs-4 text-primary"></i>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                3
                                            </span>
                                        </div>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary position-relative"&gt;
    Inbox
    &lt;span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"&gt;
        99+
    &lt;/span&gt;
&lt;/button&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Sizes -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Badge Sizes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <span class="badge bg-primary me-2 mb-2" style="font-size: 0.6rem;">Small</span>
                                        <span class="badge bg-primary me-2 mb-2">Default</span>
                                        <span class="badge bg-primary me-2 mb-2" style="font-size: 1.1rem;">Large</span>
                                        <br>
                                        <span class="badge rounded-pill bg-success me-2 mb-2" style="font-size: 0.6rem;">Small Pill</span>
                                        <span class="badge rounded-pill bg-success me-2 mb-2">Default Pill</span>
                                        <span class="badge rounded-pill bg-success me-2 mb-2" style="font-size: 1.1rem;">Large Pill</span>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;span class="badge bg-primary" style="font-size: 0.6rem;"&gt;Small&lt;/span&gt;
&lt;span class="badge bg-primary"&gt;Default&lt;/span&gt;
&lt;span class="badge bg-primary" style="font-size: 1.1rem;"&gt;Large&lt;/span&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Headings with Badges -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Headings with Badges</h5>
                                </div>
                                <div class="card-body">
                                    <div class="element-preview-container">
                                        <h1>Example heading <span class="badge bg-secondary">New</span></h1>
                                        <h2>Example heading <span class="badge bg-secondary">New</span></h2>
                                        <h3>Example heading <span class="badge bg-secondary">New</span></h3>
                                        <h4>Example heading <span class="badge bg-secondary">New</span></h4>
                                        <h5>Example heading <span class="badge bg-secondary">New</span></h5>
                                        <h6>Example heading <span class="badge bg-secondary">New</span></h6>
                                    </div>
                                    <div class="element-code-block">
                                        <pre><code class="language-html">&lt;h1&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h1&gt;
&lt;h2&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h2&gt;
&lt;h3&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h3&gt;
&lt;h4&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h4&gt;
&lt;h5&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h5&gt;
&lt;h6&gt;Example heading &lt;span class="badge bg-secondary"&gt;New&lt;/span&gt;&lt;/h6&gt;</code></pre>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(this)">
                                        <i class="bi bi-clipboard me-2"></i>Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>

            <?php include '../app/includes/footer.php'; ?>

        </div>
    </div>

    <!-- Scripts -->
    
    <!-- Prism.js for syntax highlighting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    
    <script>
        function copyCode(button) {
            const codeBlock = button.parentElement.querySelector('.element-code-block pre code');
            navigator.clipboard.writeText(codeBlock.textContent).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check me-2"></i>Copied!';
                button.classList.add('btn-success');
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                }, 2000);
            });
        }

        function copyAllCode() {
            const allCodeBlocks = document.querySelectorAll('.element-code-block pre code');
            let allCode = '';
            allCodeBlocks.forEach(block => {
                allCode += block.textContent + '\n\n';
            });
            navigator.clipboard.writeText(allCode).then(() => {
                alert('All code copied to clipboard!');
            });
        }

        function initializeSyntaxHighlighting() {
            // Initialize Prism.js highlighting
            if (typeof Prism !== 'undefined') {
                Prism.highlightAll();
            }
        }

        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.querySelector('[data-sidebar-toggle]');
            const wrapper = document.getElementById('admin-wrapper');

            if (toggleButton && wrapper) {
                toggleButton.addEventListener('click', () => {
                    wrapper.classList.toggle('sidebar-collapsed');
                });
            }

            // Apply syntax highlighting
            initializeSyntaxHighlighting();
        });
    </script>
</body>
</html>