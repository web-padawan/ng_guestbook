<form action="?mod=extra-config&plugin=guestbook&action=insert_field" method="POST" name="fieldForm">
  <fieldset class="admGroup">
    <legend class="title">���������� ������ ����</legend>
    <table border="0" cellspacing="1" cellpadding="1" class="content">
      <tr class="contRow1">
        <td width="20%">ID ����</td>
        <td><input type="text" name="id" size="50"></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">����� ����</td>
        <td><input type="text" name="name" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">����������� (placeholder)</td>
        <td><input type="text" name="placeholder" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">�������� �� ���������</td>
        <td><input type="text" name="default_value" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">�����������?</td>
        <td><input type="checkbox" name="required" /></td>
      </tr>
      <tr class="contRow1">
        <td colspan=2 align="center">
          <input type="submit" class="button" value="���������">
        </td>
      </tr>
    </table>
  </fieldset>
</form>
