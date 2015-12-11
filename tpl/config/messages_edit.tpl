<form method="post" action="" name="form">
  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['message_edit_title'] }} {{ field.name }}</legend>
    <table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_date'] }}</label></td>
        <td>{{ postdate|date("j.m.Y H:i") }}</td>
      </tr>
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_ip'] }}</label></td>
        <td>{{ ip }}</td>
      </tr>
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_author'] }} <b style="color:red">{{ lang['gbconfig']['message_required'] }}</b></label></td>
        <td><input type="text" name="author" value="{{ author }}" /></td>
      </tr>
      {% for field in fields %}
      <tr class="contRow1">
        <td><label>{{ field.name }} {% if field.required %}<b style="color:red">{{ lang['gbconfig']['message_required'] }}</b>{% endif %}</label></td>
        <td><input type="text" name="{{ field.id }}" value="{{ field.value }}" {% if field.required %}required{% endif %} /></td>
      </tr>
      {% endfor %}
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_content'] }} <b style="color:red">{{ lang['gbconfig']['message_required'] }}</b></label></td>
        <td><textarea type="text" name="message" rows="8" cols="100">{{ message }}</textarea></td>
      </tr>
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_answer'] }}</label></td>
        <td><textarea type="text" name="answer" rows="8" cols="100">{{ answer }}</textarea></td>
      </tr>
      <tr class="contRow1">
        <td><label>{{ lang['gbconfig']['message_status'] }}</label></td>
        <td>
          <select name="status" class="bfstatus">
            <option value="1" {% if status  == '1' %}selected{% endif %}>{{ lang['gbconfig']['message_active'] }}</option>
            <option value="0" {% if status == '0' %}selected{% endif %}>{{ lang['gbconfig']['message_inactive'] }}</option>
          </select>
        </td>
      </tr>
      <tr class="contRow1">
        <td colspan="2">
          <span class="right_s">
            <input type="reset" class="button" value="{{ lang['gbconfig']['message_reset'] }}" />&nbsp;
            <input name="submit" type="submit" class="button" value="{{ lang['gbconfig']['message_submit'] }}"/>
          </span>
        </td>
      </tr>
    </table>
  </fieldset>
</form>
