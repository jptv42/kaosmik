<!-- En-tête de la conversation affichant le nom du destinataire -->
<div class="row mb-3 align-items-center">
    <div class="col">
        <div>
            <h1 class="shadow text-white">On papote avec <?= $receiver->username; ?></h1>
            <span class="text-white">On reste cordial</span>
        </div>
    </div>
</div>

<!-- Corps principal de la fenêtre de chat -->
<div class="row">
    <div class="col">
        <div class="card" style="height: 75vh;">
            <!-- Conteneur défilant qui contient les bulles de messages -->
            <div class="card-body scrollable">
                <div class="chat">
                    <div class="chat-bubbles">
                        <!-- Boucle PHP : Parcourt chaque message et génère une "View Cell" (bulle) adaptée -->
                        <?php foreach($messages as$message): ?>
                            <?= view_cell('BubbleMessageCell', [
                                    'chatMessage' => $message,
                                    'sender_context' => $message->id_sender ==$logged_user->id // Vrai si c'est moi l'expéditeur
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Pied de page : Zone de saisie et bouton d'envoi -->
            <div class="card-footer">
                <div class="row">
                    <div class="col">
                        <!-- Champ de texte pour écrire le message -->
                        <textarea id="content-message" class="form-control" rows="1" placeholder="Ecrivez votre message"></textarea>
                    </div>
                    <div class="col-auto">
                        <!-- Bouton d'envoi qui stocke l'ID du destinataire dans un attribut HTML (data-receiver) -->
                        <button id="send-message" class="btn btn-kaosmik" data-receiver="<?= $receiver->id; ?>">Envoyer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Partie JavaScript : Gère l'interactivité, l'envoi et le temps réel (AJAX) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sélection des éléments HTML clés de la page
        const contentMessage = document.getElementById('content-message');
        const sendButton = document.getElementById('send-message');
        const receiver = sendButton.getAttribute('data-receiver'); // Récupère l'ID du destinataire
        const chatBubbles = document.querySelector('.chat-bubbles');
        const chatContainer = document.querySelector('.card-body.scrollable');

        // Mémorise la date du dernier message affiché (ou l'heure actuelle si le chat est vide)
        let lastMessageDate = "<?= !empty($messages) ? end($messages)->created_at : date('Y-m-d H:i:s'); ?>";

        // Fonction pour faire défiler automatiquement le chat vers le bas
        function scrollToBottom(smooth = true) {
            if(chatContainer) {
                chatContainer.scrollTo({
                    top: chatContainer.scrollHeight,
                    behavior: smooth ? 'smooth' : 'instant'
                });
            }
        }

        // Fait descendre le chat tout en bas dès l'ouverture de la page (sans animation)
        scrollToBottom(false);

        // Fonction pour envoyer un message au serveur en AJAX (sans recharger la page)
        function sendMessage() {
            const textMessage = contentMessage.value.trim();
            if(!textMessage) return; // Stoppe si le champ est vide

            sendButton.disabled = true; // Désactive le bouton pour éviter les doubles clics

            // Prépare les données à envoyer
            const formData = new FormData();
            formData.append('receiver_id', receiver);
            formData.append('message', textMessage);

            // Envoi de la requête POST vers le serveur
            fetch('<?= base_url("/chat/send"); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (!response.ok) throw new Error('Erreur de réseau');
                return response.json();
            }).then(data => {
                if (data.success) {
                    // Si l'envoi réussit : ajoute la nouvelle bulle à la fin, vide le champ et descend en bas
                    chatBubbles.insertAdjacentHTML('beforeend', data.html);
                    contentMessage.value = '';
                    scrollToBottom();
                } else {
                    alert(data.error || 'Impossible d\'envoyer le message');
                }
            }).catch(error => {
                console.error('Erreur: ', error);
                alert('Une erreur est survenue lors de l\'envoi du message');
            }).finally( () => {
                sendButton.disabled = false; // Réactive le bouton
            })
        }

        // Déclenche l'envoi lorsqu'on clique sur le bouton "Envoyer"
        sendButton.addEventListener('click', sendMessage);

        // Déclenche l'envoi lorsqu'on appuie sur la touche "Entrée" (sans la touche Maj/Shift)
        contentMessage.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Fonction pour récupérer les nouveaux messages en arrière-plan (Temps réel / Long Polling)
        function fetchNewMessages() {
            const url = `<?= base_url('/chat/new-messages');?>?receiver_id=${receiver}&last_date=${encodeURIComponent(lastMessageDate)}`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => res.json()
            ).then(data => {
                // Si de nouveaux messages sont trouvés
                if(data.success && data.html !== '') {
                    chatBubbles.insertAdjacentHTML('beforeend', data.html); // Ajoute les messages
                    lastMessageDate = data.latest_date; // Met à jour la date de référence
                    scrollToBottom(true); // Fait défiler le chat
                }
            }).catch(err => console.error('Erreur de récupération des messages : ', err));
        }

        // Exécute la fonction fetchNewMessages toutes les 1000 millisecondes (1 seconde)
        setInterval(fetchNewMessages, 1000);
    })

    // 1. Préparation des données (via FormData ou un objet JSON)
    const formData = new FormData();
    formData.append('mon_champ', 'valeur_du_champ');

    // 2. Appel de fetch avec l'URL et les options
    fetch('/votre-url-serveur', {
        method: 'POST',
        body: formData, // Les données envoyées
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Repère AJAX pour le serveur
        }
    })
        .then(response => {
            // 3. Vérification de la réponse réseau
            if (!response.ok) throw new Error('Erreur réseau ou serveur');
            return response.json(); // On traduit la réponse du serveur en format JSON
        })
        .then(data => {
            // 4. Traitement des données reçues du serveur
            if (data.success) {
                console.log('Succès !', data);
            } else {
                console.error('Erreur métier : ', data.error);
            }
        })
        .catch(error => {
            // 5. Gestion des erreurs globales (coupure réseau, bug de code...)
            console.error('Erreur critique : ', error);
        });




<script>

</script>

