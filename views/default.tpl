<h1 class="H">{t c='Member List'}</h1>

<div class="PageControls Top">
{$Pager}
</div>

<table>
    <thead>
        <tr>
            <th>{t c='Avatar'}</th>
            <th>{t c='Name'}</th>
            <th>{t c='Last Online'}</th>
            <th>{t c='Member Since'}</th>
            <th>{order_link text='Last Online' field='DateLastActive'}</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$Members item=Member}
    {assign var="Gender" value=$Member.Gender|upper}
        <tr class="{t c="Gender$Gender"}">
            <td>{user_photo user=$Member}</td>
            <td>{$Member.Name}</td>
            <td>{$Member.DateLastActive|date}</td>
            <td>{$Member.DateInserted|date}</td>
            <td>{$Member.UserID}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
<h2>
{assign var="arrTest" value='eins'}
{$arrTest}
</h2>
<div class="PageControls Bottom">
{$Pager}
</div>

<form>
    <span>{t c='Sort by'}</span>
    <select name="SortField" id="SortField">
        <option value="Name">{t c='Name'}</option>
        <option value="DateLastActive">{t c='Last Online'}</option>
        <option value="DateInserted">{t c='Member Since'}</option>
    </select>
    <select name="SortOrder" id="SortOrder" onchange="">
        <option value="asc">{t c='Ascending'}</option>
        <option value="desc">{t c='Descending'}</option>
    </select>
    <div class="ButtonWrap">
        {link text='GO!' path='/vanilla/members' class='Button GoButton'}
    </div>
</form>
{literal}
<script>
    document.querySelector('.GoButton').addEventListener(
        'click',
        function(e){
            var elSort = document.getElementById('SortField');
            var elOrder = document.getElementById('SortOrder');
            console.log(gdn.definition('MembersLink'));
            console.log(elSort.value);
            console.log(elOrder.value);
            e.preventDefault();
        }
    );
</script>
{/literal}