<form action="?mod=extra-config&plugin=guestbook&action=update_field&id={{ field.id }}" method="POST" name="fieldForm">
  <fieldset class="admGroup">
    <legend class="title">Редактирование поля {{ field.name }}</legend>
    <table border="0" cellspacing="1" cellpadding="1" class="content">
      <tr class="contRow1">
        <td width="20%">ID поля</td>
        <td>{{ field.id }}</td>
      </tr>
      <tr class="contRow1">
        <td width="20%">Метка поля</td>
        <td><input type="text" name="name" value="{{ field.name }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">Заполнитель (placeholder)</td>
        <td><input type="text" name="placeholder" value="{{ field.placeholder }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">Значение по умолчанию</td>
        <td><input type="text" name="default_value" value="{{ field.default_value }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">Обязательно?</td>
        <td><input type="checkbox" name="required" value="{{ field.required }}" {% if field.required %}checked="cheecked"{% endif %}/></td>
      </tr>
      <tr class="contRow1">
        <td colspan=2 align="center">
          <input type="submit" class="button" value="Сохранить">
        </td>
      </tr>
    </table>
  </fieldset>
</form>
