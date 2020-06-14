<?php
$sql = "
SELECT id, part_name, location, quantity
FROM Inventory

";

$sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute([]);
$inventory = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <section>
        <div class="d-none d-sm-block" id="titlePage">
            <div class="container">
                <h1>Inventaires</h1>
            </div>
            <hr class="headerSep">
        </div>

        <div class="container" id="inventoryContainer">
            <button class="btn btn-primary" style="margin-bottom: 1em" data-toggle="modal" data-target="#addItem">Ajouter un objet</button>
            <table id="inventoryTable" class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nom du produit</th>
                    <th scope="col">Emplacement</th>
                    <th scope="col">Quantité</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody id="inventoryContent">
                <?php foreach ($inventory as $key => $item):?>
                <tr id="item<?=$item['id']?>">
                    <th class="number" scope="row"><?=$key+1?></th>
                    <td class="part_name"><?=htmlspecialchars($item['part_name'])?></td>
                    <td class="location"><?=htmlspecialchars($item['location'])?></td>
                    <td class="quantity"><?=htmlspecialchars($item['quantity'])?></td>
                    <td>
                        <span>
                            <a href="#" class="editBtn" data-item_id="<?=$item['id']?>" onclick="editInventory(this)"><i class="fas fa-edit" style="color: #005d88"></i></a>
                        </span>
                        <span>
                            <a href="#" class="delBtn" data-item_id="<?=$item['id']?>"><i class="fas fa-trash-alt" style="color: #cc0013"></i></a>
                        </span>
                    </td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </section>
</main>


<!-- Modal -->
<div id="addItem" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un objet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="addItemForm" class="form-group">
                    <label for="inputItemName">Nom de l'objet:</label>
                    <input type="text" class="form-control" id="inputItemName" required>
                    <label for="inputLocation">Emplacement:</label>
                    <input type="text" class="form-control" id="inputLocation" required>
                    <label for="inputQuantity">Quantité:</label>
                    <input type="number" class="form-control" id="inputQuantity" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="addItem(this)">Ajouter</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>