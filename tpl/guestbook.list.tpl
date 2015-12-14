<section class="content container">
  <div class="row">

    <article class="content-text col-xs-12">
      <h1>������</h1>
      <h2>�� ������� ������� ����� ��������</h2>
      <p>�������� �� 100 ���� �� ������������� ������������� � ������������ ������. � ������ ���������� ����������� iPhone � ��������, ������� ���������� ����� ����� ��������, ���������� �����������, �� ����������� ������� ��������������� ������� �������/�������������.</p>
    </article><!-- /.content-text -->

  </div>
</section><!-- /.content -->

<section class="reviews-page container">

{% if (errors|length > 0) %}
    {% for error in errors %}
<div class="msge alert alert-error">{{error}}<br/></div>
    {% endfor %}
{% endif %}

{% if (success|length > 0) %}
    {% for succ in success %}
<div class="msgi alert alert-success">{{succ}}<br/></div>
    {% endfor %}
{% endif %}

  <div class="reviews-page-list row">

{% if (total_count > 0) %}

    {% for comment in comments %}
    <div class="review col-xs-12 col-sm-6">
      <div class="review-inner">
        <div class="review-header">
          {% if comment.social %}
            {% if comment.social.Vkontakte.photo %}
              {% set ava = comment.social.Vkontakte.photo %}
              {% set link = comment.social.Vkontakte.link %}
            {% elseif comment.social.Facebook.photo %}
              {% set ava = comment.social.Facebook.photo %}
              {% set link = comment.social.Facebook.link %}
            {% elseif comment.social.Google.photo %}
              {% set ava = comment.social.Google.photo %}
              {% set link = comment.social.Google.link %}
            {% endif %}
          {% else %}
            {% set ava = '/uploads/avatars/noavatar.gif' %}
          {% endif %}
          <div class="person-photo">
            {% if comment.social %}<a href="{{ link }}">{% endif %}
            <img src="{{ ava }}" width="60" height="60">
            {% if comment.social %}</a>{% endif %}
          </div>
          <div class="person-name">{{ comment.fields[0].value }} {{ comment.fields[2].value }}</div>
          <div class="review-date">{{ comment.date }}</div>
          <div class="review-subject">������������� - {{ comment.fields[1].value }}</div>
        </div>
        <div class="review-caption"><p>{{comment.message}}</p></div>
        {% if(global.user.id) and (global.user.status == '1') %}
        <div class="review-caption"><p>{{comment.ip}} / <a href="{{comment.edit}}">�������������</a> / <a href="{{comment.del}}">�������</a></p></div>
        {% endif %}
        <div class="review-social">
          <ul class="social-links social-links-default list-inline">
            {% if comment.social.Vkontakte %}<li class="active"><a href="{{ link }}"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>{% endif %}
            {% if comment.social.Google %}<li class="active"><a href="{{ link }}"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>{% endif %}
            {% if comment.social.Facebook %}<li class="active"><a href="{{ link }}"><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></a></li>{% endif %}
            <!-- <li><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></li> -->
          </ul>
        </div>
      </div>
    </div><!-- /.review -->
    {% endfor %}
{% endif %}

  </div><!-- /.reviews-feed-list -->

{% if (total_count > perpage) %}
  <ul class="pagination">
    {pages}
  </ul>
{% endif %}

</section><!-- /.reviews-page -->

{% if(use_guests) %}
<div class="container">
  <div class="msgi alert alert-success">������ ������ ��������� ������. �����������������.</siv>
</div>
{% else %}
<form name="form" method="post" action="{{ php_self }}?action=add" class="review-form verifiable-form container">
  <fieldset class="row">
    {% if(global.user.name) %}
      ��� ����������� ����� ����������� �� ����� <strong>{{global.user.name}}</strong>
      <input type="hidden" name="author" value="{{global.user.name}}"/>
    {% else %}
    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>{{ fields[0].name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields[0].placeholder }}" name="{{ fields[0].id }}" value="{{ fields[0].default_value }}">
      </div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>{{ fields[2].name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields[2].placeholder }}" name="{{ fields[2].id }}" value="{{ fields[2].default_value }}">
      </div>
    </div>

    <input type="hidden" name="author" value="guest"/>

  {% endif %}

    <div class="col-xs-12 col-sm-4 col-md-3 col-md-offset-3">
      <div class="form-group">
        <label>{{ fields[1].name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields[1].placeholder }}" name="{{ fields[1].id }}" value="{{ fields[1].default_value }}">
      </div>
    </div>

    <div class="col-xs-12 col-md-12">
      <div class="form-group">
        <label>��� �����</label>
        <textarea name="content" id="content" class="form-control required" placeholder="{{placeholder.message}}">{{field.message}}</textarea>
      </div>
    </div>

    <div class="col-xs-12 col-sm-3 col-md-2">
      <div class="form-group">
        <button name="submit" type="submit" class="btn btn-danger">��������� �����</button>
        <input type="hidden" name="ip" value="{{ip}}"/>
      </div>
    </div>

    <div class="social-links-wrap col-xs-12 col-sm-3 col-md-3 col-lg-2">
      <ul class="social-links social-links-default list-inline">
        <li id="Vkontakte_li"><a id="vk" href="#"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>
        <li id="Google_li"><a id="gg" href="#"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>
        <li id="Facebook_li"><a id="fb" href="#"><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></a></li>
        <li><a href="#"><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></a></li>
      </ul>
    </div>

    <div class="form-caption col-xs-12 col-md-7">
      <p>���������� ���� ������� � ���������� ����, �������� ����� ����� ������������!<br>
      <span class="text-muted">������ ������� �� ������ ������� ������� ������ ����������</span></p>
    </div>

    {% if(use_captcha) %}{{captcha}}{% endif %}

    <input type="hidden" name="Vkontakte_id" id="Vkontakte_id" value="" />
    <input type="hidden" name="Facebook_id" id="Facebook_id" value="" />
    <input type="hidden" name="Google_id" id="Google_id" value="" />
  </fieldset>
</form>
<script>
  (function() {
    var fb = document.getElementById('fb'),
        vk = document.getElementById('vk'),
        gg = document.getElementById('gg');

    fb.onclick = function(ev) {ev.preventDefault(); var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Facebook', 'FB', 'width=420,height=400'); n.focus(); }
    vk.onclick = function(ev) {ev.preventDefault(); var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Vkontakte', 'VK', 'width=420,height=400'); n.focus(); }
    gg.onclick = function(ev) {ev.preventDefault(); var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Google', 'Google', 'width=420,height=400'); n.focus(); }
  })();
</script>
{% endif %}
