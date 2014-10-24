<?php

echo $this->Html->script('//tinymce.cachefly.net/4.1/tinymce.min.js');
echo $this->Html->script('Libraries/StaticContent/edit');

echo '<script type="text/javascript">
    tinymce.init({
        selector: "div.editStaticContent",
        plugins: [
             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
             "save table contextmenu directionality emoticons template paste textcolor"
       ],
       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
    });
</script>';

echo $this->Html->tag(
    'div',
    $staticContent,
    [
        'class' => 'editStaticContent',
        'uid'   => $uid
    ]
);

echo $this->Html->tag(
    'input',
    '',
    [
        'class' => 'editStaticContentSaveButton',
        'type'  => 'button',
        'uid'   => $uid,
        'value' => 'Save'
    ]
);