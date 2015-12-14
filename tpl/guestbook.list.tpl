<section class="content container">
  <div class="row">

    <article class="content-text col-xs-12">
      <h1>Отзывы</h1>
      <h2>Мы дорожим мнением наших клиентов</h2>
      <p>Гарантия от 100 дней на установленные комплектующие и исправленный дефект. В случае повторного поступления iPhone с дефектом, который устранялся ранее нашим сервисом, проводится диагностика, по результатам которой устанавливается причина дефекта/неисправности.</p>
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
          <div class="person-photo"><img src="/uploads/images/review/ava1.png"> </div>
          <div class="person-name">{{comment.name}}{{comment.secondname}}</div>
          <div class="review-date">{{ comment.date }}</div>
          <div class="review-subject">Ремонтировали - {{comment.item}}</div>
          <!-- begin fields -->
          {% for field in comment.fields %}
            {% if field.value %}<div class="person-name">{{ field.name }} - {{ field.value }}</div>{% endif %}
          {% endfor %}
          <!-- end fields -->
        </div>
        <div class="review-caption"><p>{{comment.message}}</p></div>
        {% if(global.user.id) and (global.user.status == '1') %}
        <div class="review-caption"><p>{{comment.ip}} / <a href="{{comment.edit}}">Редактировать</a> / <a href="{{comment.del}}">Удалить</a></p></div>
        {% endif %}
        <div class="review-social">
          <ul class="social-links social-links-default list-inline">
            <li class="active"><a href="#"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>
            <li class="active"><a href="#"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>
            <li><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></li>
            <li><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></li>
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
  <div class="msgi alert alert-success">Гостям нельзя оставлять отзывы. Зарегистрируйтесь.</siv>
</div>
{% else %}
<form name="form" method="post" action="{{ php_self }}?action=add" class="review-form verifiable-form container">
  <fieldset class="row">
{% if(global.user.name) %}
Ваш комментарий будет опубликован от имени <strong>{{global.user.name}}</strong>
<input type="hidden" name="author" value="{{global.user.name}}"/>
{% else %}
    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>Ваше имя</label>
        <input type="text" class="form-control required" placeholder="{{placeholder.message}}" name="{{field.secondname}}" value="{{field.name}}">
      </div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>Ваша фамилия</label>
        <input type="text" class="form-control required" placeholder="{{placeholder.message}}" name="{{field.secondname}}" value="{{field.secondname}}">
      </div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-3 col-md-offset-3">
      <div class="form-group">
        <label>Что вы ремонтировали у нас</label>
        <input type="text" class="form-control required" placeholder="{{placeholder.message}}" name="{{field.secondname}}" value="{{field.item}}">
      </div>
    </div>
{% endif %}

    {% for field in fields %}
    <div class="col-xs-12 col-md-12">
      <div class="form-group">
        <label>{{ field.name }}</label>
        <input type="text" id="{{ field.id }}" name="{{ field.id }}" class="form-control {% if field.required %}required{% endif %}" placeholder="{{ field.placeholder }}" value="{{ field.default_value }}" {% if field.required %}required{% endif %}>
      </div>
    </div>
    {% endfor %}

    <div class="col-xs-12 col-md-12">
      <div class="form-group">
        <label>Ваш отзыв</label>
        <textarea name="content" id="content" class="form-control required" placeholder="{{placeholder.message}}">{{field.message}}</textarea>
      </div>
    </div>

    <div class="col-xs-12 col-sm-3 col-md-2">
      <div class="form-group">
        <button name="submit" type="submit" class="btn btn-danger">Отправить отзыв</button>
        <input type="hidden" name="ip" value="{{ip}}"/>
      </div>
    </div>

    <div class="social-links-wrap col-xs-12 col-sm-3 col-md-3 col-lg-2">
      <ul class="social-links social-links-default list-inline">
        <li><a id="vk" href="#"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>
        <li><a id="gg" href="#"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>
        <li><a id="fb" href="#"><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></a></li>
        <li><a href="#"><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></a></li>
      </ul>
    </div>

    <div class="form-caption col-xs-12 col-md-7">
      <p>Прикрепите свой профиль в социальной сети, сделайте отзыв более убедительным!<br>
      <span class="text-muted">Просто нажмите на иконку соцсети которую хотите прикрепить</span></p>
    </div>

    {% if(use_captcha) %}{{captcha}}{% endif %}

  </fieldset>
</form>
<script>
  (function() {
    var fb = document.getElementById('fb'),
        vk = document.getElementById('vk'),
        gg = document.getElementById('gg');

    fb.onclick = function() { var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Facebook', 'FB', 'width=420,height=400'); n.focus(); }
    vk.onclick = function() { var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Vkontakte', 'VK', 'width=420,height=400'); n.focus(); }
    gg.onclick = function() { var n = window.open('http://web-padavan.pp.ua/plugin/guestbook/social/?provider=Google', 'Google', 'width=420,height=400'); n.focus(); }
  })();
</script>
{% endif %}
