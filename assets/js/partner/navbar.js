document.addEventListener("DOMContentLoaded", function () {
    const profileIcon = document.querySelector(".profile-icon");
    const dropdownMenu = document.getElementById("profileDropdown");

    profileIcon.addEventListener("click", (e) => {
        e.stopPropagation(); // Prevent event bubbling
        dropdownMenu.classList.toggle("active"); // Toggle the active class
    });

    // Close the dropdown when clicking outside
    document.addEventListener("click", () => {
        dropdownMenu.classList.remove("active");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const sunIcon = document.querySelector(".sun-icon");
    const moonIcon = document.querySelector(".moon-icon");

    // Check and apply the stored theme preference
    const currentTheme = localStorage.getItem("theme");
    if (currentTheme === "dark") {
        document.body.classList.add("dark-mode");
        sunIcon.style.display = "none";
        moonIcon.style.display = "inline";
    }

    // Toggle theme on sun/moon icon click
    function toggleTheme() {
        const isDarkMode = document.body.classList.toggle("dark-mode");
        if (isDarkMode) {
            // Dark mode
            sunIcon.style.display = "none";
            moonIcon.style.display = "inline";
            localStorage.setItem("theme", "dark");
        } else {
            // Light mode
            sunIcon.style.display = "inline";
            moonIcon.style.display = "none";
            localStorage.setItem("theme", "light");
        }
    }

    // Add event listeners to the icons
    sunIcon.addEventListener("click", toggleTheme);
    moonIcon.addEventListener("click", toggleTheme);
});

// Fonction pour déterminer l'icône selon le type de notification
function getIcon(type) {
    switch (type) {
        case 'info': return 'bi-info-circle';
        case 'success': return 'bi-check-circle';
        case 'warning': return 'bi-exclamation-circle';
        case 'danger': return 'bi-x-circle'; // ← ajouté
        default: return 'bi-bell';
    }
}
fetch('/api/user/avatar')
    .then(response => response.json())
    .then(data => {
        if (data.avatarUrl && data.avatarUrl.trim() !== "") {
            // Met à jour l'avatar dans l'élément avec la classe 'profile-icon'
            document.querySelector('.profile-icon').src = data.avatarUrl;
        } else {
            console.error('L\'avatar n\'a pas été trouvé ou est vide.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la récupération de l\'avatar:', error);
    });

    // Fonction pour formater l'heure de la notification
    function formatTime(datetimeStr) {
        const date = new Date(datetimeStr);
        return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    }

    // Fonction pour supprimer une notification spécifique
    function deleteNotification(id) {
        fetch(`/api/notifications/delete/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationItem = document.getElementById(`notification-${id}`);
                if (notificationItem) {
                    notificationItem.remove();  // Retirer la notification du DOM
                    updateNotificationCount();   // Met à jour le compteur
                }
            } else {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression de la notification:', error);
        });
    }

    // Fonction pour mettre à jour le compteur de notifications
   function updateNotificationCount() {
    const notificationCount = document.getElementById("notificationCount");
    const items = document.querySelectorAll(".notification-item");

    const count = items.length;
    notificationCount.textContent = count > 0 ? count : "";
    notificationCount.style.display = count > 0 ? "inline-block" : "none";

    // Optionnel : mettre à jour dynamiquement le titre
    const notificationTitle = document.getElementById('notificationTitle');
    notificationTitle.textContent = count > 0 ? `${count} Notifications` : "Aucune notification";
}

    // Fonction pour marquer toutes les notifications comme lues
    function markAllAsRead() {
    fetch('/api/notifications/mark-read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Marquer toutes les notifications visuellement comme lues
            const items = document.querySelectorAll(".notification-item");
            items.forEach(item => {
                item.classList.add("read");
            });
            updateNotificationCount();
        } else {
            alert(data.error || "Une erreur s'est produite.");
        }
    })
    .catch(error => {
        console.error("Erreur lors du marquage des notifications comme lues:", error);
    });
}

    // Fonction pour supprimer toutes les notifications
    function clearAllNotifications() {
        fetch('/api/notifications/clear', {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                // Remove all notification items from the DOM
                const items = document.querySelectorAll(".notification-item");
                items.forEach(item => item.remove());

                // Display a message if no notifications are left
                const emptyMessage = document.createElement("div");
                emptyMessage.textContent = "Aucune notification.";
                emptyMessage.style.padding = "1rem";
                emptyMessage.style.textAlign = "center";
                emptyMessage.style.color = "#777";
                document.querySelector('.notification-items').appendChild(emptyMessage);

                // Update the notification count
                const notificationCount = document.getElementById("notificationCount");
                updateNotificationCount();
  
            } else {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression de toutes les notifications:', error);
        });
    }

    // Chargement du DOM
    document.addEventListener("DOMContentLoaded", function () {
        const bell = document.getElementById("toggleNotifications");
        const popup = document.getElementById("notificationPopup");
        const notificationCount = document.getElementById("notificationCount");
        const content = document.getElementById("notificationItems");
        const notificationTitle = document.getElementById('notificationTitle');  // Pour le titre dynamique

        // Lorsque l'utilisateur clique sur l'icône de notification
        bell.addEventListener("click", () => {
            popup.classList.toggle("active");
        });

        // Fermer la fenêtre des notifications si l'utilisateur clique ailleurs
        document.addEventListener("click", (e) => {
            if (!popup.contains(e.target) && !bell.contains(e.target)) {
                popup.classList.remove("active");
            }
        });

        // Récupérer les notifications via l'API
        fetch('/api/notifications')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error); // Affiche une alerte si l'utilisateur n'est pas connecté
                } else {
                    // Mettre à jour le nombre de notifications dans le badge
                    notificationCount.textContent = data.length > 0 ? data.length : "0";

                    // Mettre à jour le titre des notifications
                    notificationTitle.textContent = data.length > 0 ? `${data.length} Notifications` : "Aucune notification";

                    // Si aucune notification, afficher un message "Aucune notification"
                    if (data.length === 0) {
                        const emptyMessage = document.createElement("div");
                        emptyMessage.textContent = "Aucune notification.";
                        emptyMessage.style.padding = "1rem";
                        emptyMessage.style.textAlign = "center";
                        emptyMessage.style.color = "#777";
                        content.appendChild(emptyMessage);
                    } else {
                        // Afficher les notifications ici
                        data.forEach(notification => {
                            const notificationItem = document.createElement('div');
                            notificationItem.classList.add('notification-item', notification.type); // Ajoute un type spécifique pour les styles
                            notificationItem.id = `notification-${notification.id}`; // Ajout d'un ID unique pour chaque notification

                            notificationItem.innerHTML = `
                                <div class="notification-icon-text">
                                    <i class="bi ${getIcon(notification.type)}"></i>
                                    <span>${notification.message}</span>
                                </div>
                                <div class="notification-meta">
                                    <span>${formatTime(notification.dateEnvoi)}</span>
                                    <!-- Icône corbeille de suppression -->
                                    <button class="btn-delete" aria-label="Supprimer cette notification" onclick="deleteNotification(${notification.id})">
                                    <i class="bi bi-trash"></i>
                                  </button>
                                </div>
                            `;

                            content.appendChild(notificationItem);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des notifications:', error);
            });
    });