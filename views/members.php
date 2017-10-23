<?php defined('APPLICATION') or die;
$members = $this->data('Members');
?>
<style>
    ul.DataList.Members > li > div{
        float: left;
    }
    .MembersGender {
        font-size: smaller;
    }
    .MembersStaff::after {
        content: "\1f451";
    }

</style>
<div class="Members">
    <div class="DataTableWrap">
        <h2 class="H"><?= $this->title() ?></h2>
        <table class="DataTable MembersTable">
        <?php
        /*
        <thead>
            <tr>
                <th></th>
            </tr>
        </thead>
        */
       ?>
        <tbody>
        <?php foreach ($members as $member): ?>
            <tr class="Item MItem <?= $member['CssClass'] ?>">
                <td class="MembersPhoto"><?= userPhoto($member, ['Size' => 'Small']) ?></td>
                <td class="MembersName"><?= userAnchor($member) ?></td>
                <td class="MembersGender">
                <?php
                    switch ($member['Gender']) {
                        case 'f':
                            echo t('&#9792;');
                            break;
                        case 'm':
                            echo t('&#9794;');
                            break;
                        default:
                            echo t('&nbsp;');
                    }
                ?>
                </td>
                <td class="MembersSince"><?= Gdn_Format::date($member['DateFirstVisit']) ?></td>
                <td class="MembersRole"><?= htmlEsc($member['Roles']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </div>
</div>

<?= $this->Pager->toString('more') ?>
