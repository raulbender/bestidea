<div id="user-menu" class="drop-down-menu volt-animate">
    <div class="user-menu-header">
        <strong><?= e($baseDTO->username) ?></strong>        
    </div>
    <a href="/login"><?= __('layout.sign_in') ?> </a>
    <hr>
    <a href="/logoff"><?= __('layout.menu_logoff') ?>  <?= icon('logoff') ?> </a>
</div>