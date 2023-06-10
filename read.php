<?php
include 'functions.php';
// Connexion
$pdo = pdo_connect_mysql();
// Récupère la page via la requête GET (param URL : page), si elle n'existe pas, la page est définie par défaut sur 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// Number of records to show on each page
$records_per_page = 5;

// Préparez l'instruction SQL et récupérez les enregistrements de notre table de contacts, LIMIT déterminera la page
$stmt = $pdo->prepare('SELECT * FROM contacts ORDER BY id LIMIT :current_page, :record_per_page');
$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
// Récupère les enregistrements afin que nous puissions les afficher dans notre modèle.
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupère le nombre total de contacts, ceci afin que nous puissions déterminer s'il devrait y avoir un bouton suivant et précédent
$num_contacts = $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
?>

<?=template_header('Read')?>

<div class="content read">
	<h2>Liste des contacts</h2>
	<a href="create.php" class="create-contact">Créer un contact</a>
	<table>
        <thead>
            <tr>
                <td>#</td>
                <td>Nom</td>
                <td>Email</td>
                <td>Téléphone</td>
                <td>Titre</td>
                <td>Créé le</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?=$contact['id']?></td>
                <td><?=$contact['name']?></td>
                <td><?=$contact['email']?></td>
                <td><?=$contact['phone']?></td>
                <td><?=$contact['title']?></td>
                <td><?=$contact['created']?></td>
                <td class="actions">
                    <a href="update.php?id=<?=$contact['id']?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$contact['id']?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div class="pagination">
		<?php if ($page > 1): ?>
		<a href="read.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_contacts): ?>
		<a href="read.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>

<?=template_footer()?>