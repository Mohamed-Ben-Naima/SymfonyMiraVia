 document.addEventListener('DOMContentLoaded', function () {
            const shareButton = document.getElementById('shareButton');
            const sharePopup = document.getElementById('sharePopup');
            const shareFacebook = document.getElementById('shareFacebook');
            const shareInstagram = document.getElementById('shareInstagram');
            const shareGmail = document.getElementById('shareGmail');

            if (shareButton && sharePopup) {
                shareButton.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const isVisible = sharePopup.classList.toggle('visible');
                    shareButton.setAttribute('aria-expanded', isVisible);

                    if (isVisible) {
                        const urlToShare = window.location.href;
                        const pageTitle = document.title || "Check this out!";
                        const shareText = `I found this interesting: ${pageTitle} - ${urlToShare}`;

                        shareFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(urlToShare)}`;
                        shareInstagram.href = `https://www.instagram.com/`; 
                        shareGmail.href = `mailto:?subject=${encodeURIComponent(pageTitle)}&body=${encodeURIComponent(shareText)}`;
                    }
                });

                document.addEventListener('click', function (event) {
                    if (sharePopup.classList.contains('visible') && !sharePopup.contains(event.target) && !shareButton.contains(event.target)) {
                        sharePopup.classList.remove('visible');
                        shareButton.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keyup', function(event) {
                    if (event.key === 'Escape' && sharePopup.classList.contains('visible')) {
                        sharePopup.classList.remove('visible');
                        shareButton.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            const apercuLink = document.getElementById('apercuLink');
            const mapLink = document.getElementById('mapLink');
            const avisLink = document.getElementById('avisLink');

            const overviewTabContent = document.getElementById('overviewTabContent');
            const clientReviewsSection = document.getElementById('client-reviews-section');
            const mapTabContent = document.getElementById('mapTabContent');
            const mapLocationNameSpan = document.getElementById('mapLocationName');
            const locationSpan = document.querySelector('.page-header .location');

            const allTabLinks = [apercuLink, mapLink, avisLink];

            function setActiveTab(activeLink) {
                allTabLinks.forEach(link => {
                    if (link) {
                        link.classList.remove('active');
                    }
                });
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }

            function showOverviewContent() {
                overviewTabContent.style.display = 'block';
                clientReviewsSection.style.display = 'block';
                mapTabContent.style.display = 'none';
            }

            function showMapContent() {
                overviewTabContent.style.display = 'none';
                clientReviewsSection.style.display = 'none';
                mapTabContent.style.display = 'block';
                if (locationSpan && mapLocationNameSpan) {
                    mapLocationNameSpan.textContent = locationSpan.textContent;
                }
            }

            if (apercuLink) {
                apercuLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    setActiveTab(apercuLink);
                    showOverviewContent();
                });
            }

            if (mapLink) {
                mapLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    setActiveTab(mapLink);
                    showMapContent();
                });
            }

            if (avisLink) {
                avisLink.addEventListener('click', function(e) {
                    setActiveTab(avisLink);
                    showOverviewContent();
                });
            }

            showOverviewContent();
            setActiveTab(apercuLink);
        });