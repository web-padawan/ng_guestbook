<form method="post" action="">
  <input type="hidden" name="mod" value="extra-config"/>
  <input type="hidden" name="plugin" value="guestbook"/>
  <input type="hidden" name="action" value="save_fields"/>

  <table width="100%" border="0">
    <tr class="contHead" align="left">
      <td width="10%">ID поля</td>
      <td width="15%">Метка поля</td>
      <td width="30%">Заполнитель</td>
      <td width="30%">Значение по умолчанию</td>
      <td width="10%">Обязательное?</td>
      <td width="5%" colspan="2">Действия</td>
    </tr>
    {% for entry in entries %}
    <tr align="left" class="contRow1">
      <td>{{ entry.id }}</td>
      <td>{{ entry.name }}</td>
      <td>{{ entry.placeholder }}</td>
      <td>{{ entry.default_value }}</td>
      <td>{{ entry.required }}</td>
      <td nowrap>
        <a href="?mod=extra-config&plugin=guestbook&action=edit_field&id={{ entry.id }}" title="Редактировать поле">
          <img src="{{ skins_url }}/images/add_edit.png" alt="EDIT" width="12" height="12" />
        </a>
      </td>
      <td nowrap>
        <a onclick="return confirm('Вы действительно хотите удалить поле {{ entry.id }}?');" href="?mod=extra-config&plugin=guestbook&action=drop_field&id={{ entry.id }}" title="Удалить поле">
          <img src="{{ skins_url }}/images/delete.gif" alt="DEL" width="12" height="12" />
        </a>
      </td>
    </tr>
    {% endfor %}
    <tr>
      <td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
        <a href="?mod=extra-config&plugin=guestbook&action=add_field">Добавить новое поле</a>
      </td>
    </tr>
  </table>
</form>
