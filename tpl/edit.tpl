{% if (error) %}
    {{error}}
{% else %}
<form method='post' action=''>
    <input type='text' name='author' value='{{author}}'>
    <br/><br/>
    <input type='text' name='phone' value='{{phone}}'>
    <br/><br/>
    <textarea name='content' id=\"content\" style='width: 95%;' rows='8'>{{message}}</textarea>
    <br/><br/>
    <textarea name='answer' id=\"answer\" style='width: 95%;' rows='8'>{{answer}}</textarea>
    <br/><br/>
    <input type='hidden' name='id' value='{{id}}'>
    <input type='submit' name='go' value='Отредактировать'>
</form>
{% endif %}
