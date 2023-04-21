
<?php if (count($logerrors) > 0): ?>

    <div>
        
        <?php foreach ($logerrors as $error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endforeach ?>

    </div>

<?php endif ?>