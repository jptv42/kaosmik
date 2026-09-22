<div class="row">
    <div class="col d-flex">
        <div>
            <h1 class="shadow text-white">La Cantina</h1>
            <span class="text-white">Ici, on recrute nos mercenaires</span>
        </div>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 fs-1" id="timer" data-seconds="<?= $remaining_seconds?>;">
                <?= $remaining_time;?>
            </span>
            <?= form_open('cantina/refresh')?>
            <button type="submit" class="btn btn-kaosmik">
                Rafraichir <span id="refresh-cost"></span>
            </button>
            <?= form_close()?>
        </div>
    </div>
</div>
<div class="row row-cols-sm-3 row-cols-1 g-3">
    <?php
    // Boucle PHP parcourant la liste de tous les héros disponibles dans la Cantina
    foreach($cantinaHeroes as $hero) : ?>
        <!-- Colonne Bootstrap individuelle accueillant la carte du héros -->
        <div class="col">
            <!-- Appel de la View Cell CodeIgniter 4 'HeroCell' en lui passant le héros courant ($hero) dans la variable 'character' -->
            <?= view_cell('HeroCell', ['character' => $hero,'context'=>'cantina']); ?>
        </div>
    <?php endforeach; // Fin de la boucle d'itération des héros ?>
</div>

<script>
document.addEventListener('DOMContentLoaded',() => {
    //Selection des zélements
   const timerElement = document.getElementById('timer');
   const refreshCostElement = document.getElementById('refresh-cost');
   if(!timerElement)return;
   //Récupération des secondes
    let remainingSeconds = parseInt(timerElement.dataset.seconds,10);
    //Sécurité : si pas valide ou 0 on rafraichit
    if(isNaN(remainingSeconds)|| remainingSeconds <= 0){
        window.location.reload();
        return;
    }
    //Conversion des secondes en date
    const formatTime = (seconds) =>{
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600)/60);
        const s = seconds % 60;
        //Fonctions fléchés
        const pad = (num) => String(num).padStart(2,'0');
        return `${pad(h)}:${pad(m)}:${pad(s)}`
    }
    //Calcul du cout de rafraichissement
    const updateRefreshCost = (seconds) =>{if!(refreshCostElement) return;
    const h = Math.floor(seconds / 3600);
    const cost = (h+1) * 10;
    refreshCostElement.textContent = cost;
    }
    //Affichage initial
    timerElement.textContent = formatTime(remainingSeconds);
    updateRefreshCost(remainingSeconds);
    //Création du décompte
    const countdown = setInterval(()=>{
        remainingSeconds--;
        if(remainingSeconds <=0){
            clearInterval(countdown);
            timerElement.textContent='00:00:00';
            window.location.reload();
            return;
        }
        timerElement.textContent = formatTime(remainingSeconds);
        //Mettre à jour le cout si on change d'heure (quand minutes et secondes passe à 59)
        if(remainingSeconds % 3600===3599){
            updateRefreshCost(remainingSeconds);
        }
    },1000);
});
</script>