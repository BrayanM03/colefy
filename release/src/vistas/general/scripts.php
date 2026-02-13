<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="<?php echo STATIC_URL; ?>js/app.js"></script>

 
<?php if (isset($js_especificos)): ?>
        <?php foreach ($js_especificos as $url): ?>
        <?php
        
        if(isset($url['module'])){ 
                $module = "type='module'";
         } else{
                $module = '';
              } ?>

            <script src="<?php echo $url; ?>" <?php echo $module; ?>></script>
        <?php endforeach; ?>
<?php endif; ?>
