class SearchBarHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.searchButton = this.$element.find("#searchBtn").get(0);
        this.searchForm = this.$element.find(".ajax-search-form").get(0);
        this.searchModal = null;

        if (this.searchButton) {
            this.searchButton.addEventListener("click", this.openSearch.bind(this));
        }

        if (this.searchForm) {
            const searchInput = this.searchForm.querySelector("#ajaxSearchInput");
            if (searchInput) {
                searchInput.addEventListener("input", this.debounce(this.fetchSearchResults.bind(this), 300));
            }
        }
    }

    onDestroy() {
        super.onDestroy();
        if (this.searchButton) {
            this.searchButton.removeEventListener("click", this.openSearch.bind(this));
        }
        this.removeExistingSearch(); // Ensure modal is removed when widget is destroyed
    }

    debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    openSearch() {
        const searchType = this.searchButton.getAttribute("data-search-type");
        if (searchType === "full") {
            this.createFullScreenSearch();
        } else {
            this.createSearchBar();
        }
    }

    createSearchBar() {
        this.removeExistingSearch();

        this.searchModal = document.createElement("div");
        this.searchModal.className = "search-bar";
        this.searchModal.innerHTML = `
            <div class="search-header">
                <button class="close-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                    </svg>
                </button>
            </div>
            <div class="search-content">
                <input type="text" id="searchInput" placeholder="Search...">
                <div id="searchResults" class="search-results"></div>
            </div>
        `;
        document.body.appendChild(this.searchModal);

        setTimeout(() => this.searchModal.classList.add("active"), 10);

        this.searchModal.querySelector(".close-btn").addEventListener("click", this.removeExistingSearch.bind(this));
        this.searchModal.querySelector("#searchInput").addEventListener("input", this.debounce(this.fetchSearchResults.bind(this), 300));
    }

    createFullScreenSearch() {
        this.removeExistingSearch();

        this.searchModal = document.createElement("div");
        this.searchModal.className = "full-screen-search";
        this.searchModal.innerHTML = `
            <button class="close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
            </button>
            <input type="text" id="searchInput" placeholder="Search...">
            <div id="searchResults" class="search-results"></div>
        `;
        document.body.appendChild(this.searchModal);

        setTimeout(() => {
            this.searchModal.classList.add("active");
            this.searchModal.querySelector("#searchInput").focus();
        }, 10);

        this.searchModal.querySelector(".close-btn").addEventListener("click", this.removeExistingSearch.bind(this));
        this.searchModal.querySelector("#searchInput").addEventListener("input", this.debounce(this.fetchSearchResults.bind(this), 300));
    }

    removeExistingSearch() {
        if (this.searchModal) {
            this.searchModal.classList.remove("active");
            setTimeout(() => {
                this.searchModal.remove();
                this.searchModal = null;
            }, 300);
        }
    }

    fetchSearchResults(event) {
        const query = event.target.value.trim();
        const resultsContainer = this.searchModal.querySelector("#searchResults");
        if (!resultsContainer) return;

        if (!query) {
            resultsContainer.innerHTML = "";
            return;
        }

        const searchElement = this.searchButton || this.searchForm;
        const postTypes = searchElement.getAttribute("data-post-types") ? searchElement.getAttribute("data-post-types").split(',') : ['post'];
        const resultsPerPage = searchElement.getAttribute("data-results-per-page") || 6;

        fetch(ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=custom_search&query=${encodeURIComponent(query)}&post_types=${encodeURIComponent(postTypes)}&results_per_page=${resultsPerPage}`
        })
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = "";
            if (!data.success || data.data.length === 0) {
                resultsContainer.innerHTML = `<p class="no-results">No results found</p>`;
                return;
            }

            const resultsGrid = document.createElement("div");
            resultsGrid.className = "search-results-grid";
            data.data.forEach((item, index) => {
                const resultItem = document.createElement("div");
                resultItem.className = "result-item fade-in";
                resultItem.style.animationDelay = `${index * 100}ms`;
                resultItem.innerHTML = `
                    <a href="${item.url}" class="result-link">
                        <img src="${item.image}" alt="${item.title}" class="result-image">
                        <p class="result-title">${item.title}</p>
                    </a>
                `;
                resultsGrid.appendChild(resultItem);
            });
            resultsContainer.appendChild(resultsGrid);
        })
        .catch(error => console.error("Error fetching search results:", error));
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/header-search.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(SearchBarHandler, { $element: $scope });
    });
});