<form action="/engine/admin.php?mod=extra-config&plugin=guestbook&action=modify" method="post" name="check_messages">
<!-- List of news start here -->
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="15%">����</td>
<td width="10%">�����</td>
<td width="10%">�������</td>
<td width="20%">���������</td>
<td width="15%">�����</td>
<td width="10%">IP</td>
<td width="10%">������</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" title="������� ���" onclick="javascript:check_uncheck_all(check_messages)" /></td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1"><a href="?mod=extra-config&plugin=guestbook&action=edit_message&id={{ entry.id }}" />{{ entry.id }}</a></td>
<td width="15%" class="contentEntry1">{{ entry.postdate|date("d.m.Y H:i:s") }}</td>
<td width="10%" class="contentEntry1">{{ entry.author }}</td>
<td width="10%" class="contentEntry1">{{ entry.phone }}</td>
<td width="20%" class="contentEntry1">{{ entry.message }}</td>
<td width="15%" class="contentEntry1">{{ entry.answer }}</td>
<td width="10%" class="contentEntry1">{{ entry.ip }}</td>
<td width="10%" class="contentEntry1">{{ entry.status }}</td>
<td width="5%" class="contentEntry1"><input name="selected_message[]" value="{{ entry.id }}" class="check" type="checkbox" /></td>
</tr>
{% else %}
<tr align="left">
<td colspan="10" class="contentEntry1">�� ������ ������� ������ �� �������.</td>
</tr>
{% endfor %}
<tr>
<td width="100%" colspan="10">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="10" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
��������: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- �������� --</option>
<option value="mass_approve">������������</option>
<option value="mass_forbidden">��������������</option>
<option value="" style="background-color: #E0E0E0;" disabled="disabled">===================</option>
<option value="mass_delete">�������</option>
</select>
<input type="submit" value="���������.." class="button" />
<br/>
</div>
</td>
</tr>
</form>

<tr>
<td width="100%" colspan="10">&nbsp;</td>
</tr>
<tr>
<td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
</tr>
</table>