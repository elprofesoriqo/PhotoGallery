<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/styles/gallery.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Image Gallery</a>
        <div class="d-flex">
            <div class="search-container">
                <!-- Search input will be injected here by JavaScript -->
            </div>
            <a href="<?php echo $viewModel->is_favorites ? '/' : '/favorites' ?>" class="btn btn-outline-primary ms-2">
                <?php echo $viewModel->is_favorites ? 'Gallery' : 'Favorites' ?>
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="page-title text-center mt-4 mb-4">
        <?php echo $viewModel->is_favorites ? 'Favorite Images' : 'Image Gallery' ?>
    </h2>

    <div class="search-container mb-4"></div>

    <div id="loading" class="loading" style="display: none;">Loading images...</div>

    <section class="image-grid">
        <?php foreach ($viewModel->images as $image) { ?>
            <div class="image-card">
                <div class="checkbox-container">
                    <input type="checkbox"
                           class="select-image"
                           value="<?php echo htmlspecialchars($image->id); ?>"
                           onchange="handleImageSelection(this)">
                </div>
                <img src="<?php echo htmlspecialchars($image->thumbnailPath); ?>"
                     alt="<?php echo htmlspecialchars($image->name); ?>"
                     onclick="showFullImage('<?php echo htmlspecialchars($image->fullPath); ?>')">
                <div class="info">
                    <div class="image-title"><?php echo htmlspecialchars($image->name); ?></div>
                    <div class="photographer-name">By: <?php echo htmlspecialchars($image->photographer); ?></div>
                </div>
            </div>
        <?php } ?>
    </section>

    <button class="action-button" onclick="<?php echo $viewModel->is_favorites ? 'removeFromFavorites()' : 'addToFavorites()' ?>">
        <?php echo $viewModel->is_favorites ? 'Remove Selected' : 'Add to Favorites' ?>
    </button>

    <div class="pagination">
        <?php
        $totalPages = ceil($viewModel->pagination->total / $viewModel->pagination->itemsPerPage);
        for ($i = 1; $i <= $totalPages; $i++) {
            ?>
            <button
                onclick="changePage(<?php echo $i; ?>)"
                class="<?php echo $viewModel->pagination->currentPage == $i ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </button>
        <?php } ?>
    </div>
</div>

<script>
    let currentSearchTerm;

    function throttle(func, limit = 500) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    function showLoading(show = true) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    function fetchImages(searchTerm, page = 1) {
        currentSearchTerm = searchTerm;
        showLoading(true);

        const viewMode = "<?php echo $viewModel->is_favorites ? 'favorites' : 'images'?>";
        fetch(`/api/${viewMode}?query=${encodeURIComponent(searchTerm)}&page=${page}`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                updateImageGrid(data.images);
                updatePagination(data.meta);
                showLoading(false);
            })
            .catch(err => {
                console.error('Failed to fetch images:', err);
                showAlert('Error loading images. Please try again.');
                showLoading(false);
            });
    }

    function handleImageSelection(checkbox) {
        const selectedIds = getSelectedImages();
        const id = checkbox.value;

        if (checkbox.checked && !selectedIds.includes(id)) {
            selectedIds.push(id);
        } else {
            const index = selectedIds.indexOf(id);
            if (index > -1) {
                selectedIds.splice(index, 1);
            }
        }

        localStorage.setItem('selected-images', JSON.stringify(selectedIds));
    }

    function changePage(page) {
        fetchImages(currentSearchTerm, page);
    }

    function showFullImage(url) {
        window.open(url, '_blank');
    }

    function showAlert(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning';
        alert.textContent = message;
        document.querySelector('.container').prepend(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    const throttledSearch = throttle((value) => fetchImages(value));

    function handleSearch(event) {
        throttledSearch(event.target.value);
    }

    function getSelectedImages() {
        const stored = localStorage.getItem('selected-images');
        return stored ? JSON.parse(stored) : [];
    }

    <?php if ($viewModel->is_favorites) { ?>
    function removeFromFavorites() {
        const selectedIds = getSelectedImages();
        if (!selectedIds.length) {
            showAlert('Please select images to remove');
            return;
        }

        fetch('/api/favorites/remove', {
            method: 'POST',
            body: JSON.stringify({ imageIds: selectedIds }),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (response.ok) {
                    localStorage.removeItem('selected-images');
                    window.location.reload();
                } else {
                    throw new Error('Failed to remove favorites');
                }
            })
            .catch(err => showAlert('Failed to remove favorites. Please try again.'));
    }
    <?php } else { ?>
    function addToFavorites() {
        const selectedIds = getSelectedImages();
        if (!selectedIds.length) {
            showAlert('Please select images to favorite');
            return;
        }

        fetch('/api/favorites/add', {
            method: 'POST',
            body: JSON.stringify({ imageIds: selectedIds }),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (response.ok) {
                    localStorage.removeItem('selected-images');
                    window.location.href = '/favorites';
                } else {
                    throw new Error('Failed to add favorites');
                }
            })
            .catch(err => showAlert('Failed to add favorites. Please try again.'));
    }
    <?php } ?>

    document.addEventListener('DOMContentLoaded', () => {
        const searchBox = `
                <input
                    type="search"
                    class="search-input"
                    placeholder="Search images..."
                    id="image-search"
                    oninput="handleSearch(event)"
                    value="<?php echo htmlspecialchars($viewModel->pagination->query ?? ''); ?>"
                >
            `;
        document.querySelector('.search-container').innerHTML = searchBox;

        // Initialize selected items from localStorage
        const selectedIds = getSelectedImages();
        selectedIds.forEach(id => {
            const checkbox = document.querySelector(`input[value="${id}"]`);
            if (checkbox) checkbox.checked = true;
        });
    });
</script>
</body>
</html>