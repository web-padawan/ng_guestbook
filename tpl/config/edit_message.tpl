<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<form method="post" action="" name="form">
<dl>
<dt><label>����</label></dt>
<dd>{{postdate|date("d.m.Y H:i:s")}}</dd>
</dl>
<dl>
<dt><label>IP</label></dt>
<dd>{{ip}}</dd>
</dl>
<dl>
<dt><label>����� [*]</label></dt>
<dd><input type="text" name="author" value="{{author}}"  /></dd>
</dl>
{% for field in fields %}
<dl>
<dt><label>{{ field.name }}</label></dt>
<dd><input type="text" name="{{ field.id }}" value="{{ field.value }}" {% if field.required %}required{% endif %} /></dd>
</dl>
{% endfor %}
<dl>
<dt><label>��������� [*]</label></dt>
<dd><textarea type="text"  name="message" rows=8 cols=100>{{message}}</textarea></dd>
</dl>
<dl>
<dt><label>�����</label></dt>
<dd><textarea type="text"  name="answer" rows=8 cols=100>{{answer}}</textarea></dd>
</dl>
<dl>
<dt><label>������</label></dt>
<dd>
		<select name="status" class="bfstatus">
		<option value="1" {% if status  == '1' %}selected{% endif %}>�������</option>
		<option value="0" {% if status == '0' %}selected{% endif %}>���������</option>
		</select>
</dd>
</dl>
<span class="right_s"><input type="reset" class="button" value="�����" />&nbsp;<input name="submit" type="submit" class="button" value="���������"/></span>
</form>
</table>

</div>



