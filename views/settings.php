<h1><?= $this->data('Title') ?></h1>
<div class="Info"><?= $this->data('Description') ?></div>
<div class="Warning"><strong><?= $this->data('Warning') ?></strong></div>

<?php
echo
	$this->Form->open(),
	$this->Form->errors(),
	$this->Form->label('Grid', 'ConfigName'),
	$this->Form->checkBoxGridGroups(
        $this->data('Permissions'),
        'ConfigName'
	),
    $this->Form->button('Save');
	$this->Form->close();






decho($this->data('Roles'));
decho($this->data('UserFields'));
