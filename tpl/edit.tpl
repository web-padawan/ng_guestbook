{% if (error) %}
  {{ error }}
{% else %}
<form method='post' action='{{ php_self }}?action=edit'>
  <div>
    <label for="author">Автор</label>
    <input type='text' name='author' id='author' value='{{ author }}' required>
  </div>
  {% for field in fields %}
  <div>
    <label for="{{ field.id }}">{{ field.name }} {% if field.required %}<b style="color:red">*</b>{% endif %}</label></td>
    <input type="text" id="{{ field.id }}" name="{{ field.id }}" value="{{ field.value }}" {% if field.required %}required{% endif %} /></td>
  </div>
  {% endfor %}
  <div>
    <label for="content">Сообщение</label>
    <textarea name='content' id="content" style='width: 95%;' rows='8'>{{ message }}</textarea>
  </div>
  <div>
    <label for="answer">Ответ</label>
    <textarea name='answer' id="answer" style='width: 95%;' rows='8'>{{ answer }}</textarea>
  </div>
  <input type='hidden' name='id' value='{{id}}'>
  <input type='submit' value='Отредактировать'>
</form>
{% endif %}
