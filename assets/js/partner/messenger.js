document.addEventListener("DOMContentLoaded", function () {
    const sendButton = document.getElementById('sendButton'); // Utilisation de l'id
    const messageInput = document.getElementById('messageInput');
    const messageContainer = document.querySelector('.message-container');
    const receiverId = document.getElementById('receiverId').value;
  
    sendButton.addEventListener('click', function () {
      const messageText = messageInput.value.trim();
      if (messageText === "") return; // Ne pas envoyer de message vide
  
      // Envoi du message via AJAX
      fetch('/send-message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Si tu utilises CSRF
        },
        body: JSON.stringify({
          message: messageText,
          receiverId: receiverId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Affichage du message dans la conversation
          const newMessage = document.createElement('div');
          newMessage.classList.add('d-flex', 'flex-row', 'justify-content-end', 'mb-3');
          newMessage.innerHTML = `
            <div>
              <div class="p-2 rounded-3 bg-primary text-white">${messageText}</div>
              <p class="small text-muted mt-1 text-end">Maintenant</p>
            </div>
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp" alt="avatar" class="rounded-circle ms-2" style="width: 45px; height: 45px;">
          `;
          messageContainer.appendChild(newMessage);
          messageInput.value = '';
          messageContainer.scrollTop = messageContainer.scrollHeight;
        } else {
          alert("Erreur lors de l'envoi du message");
        }
      })
      .catch(error => {
        console.error('Erreur AJAX:', error);
        alert('Une erreur s\'est produite lors de l\'envoi du message.');
      });
    });
  });
  