var moneymaker2_total_count = json['total_count'];
var moneymaker2_total_sum = json['total_price'];
$('#cart > .dropdown-toggle #cart-total').html(moneymaker2_total_sum);
$('#cart > .dropdown-toggle .fa-stack .fa-stack-1x, .navbar-cart-toggle .fa-stack .fa-stack-1x').html(moneymaker2_total_count);
$('#cart > ul').load('index.php?route=common/cart/info ul li');
$('#popupModal').find('.modal-body').load('index.php?route=common/cart/info ul', function() {
    $('#popupModal .modal-header .close').addClass('hidden');
    $('#popupModal .modal-body > ul').removeClass('dropdown-menu keep-open');
    $('#popupModal .modal-body > ul').addClass('list-unstyled');
    $('#popupModal .modal-body .btn-primary').parent().parent().prepend('<div class="panel panel-info"><div class="panel-heading text-center"><small>' + json['success'] + '</small></div></div>');
    $('#popupModal').find('.modal-title').load('index.php?route=common/cart/info #cart-total', function () {
        $('#popupModal .modal-title').text(json['total']);
        $('#popupModal').modal();
    });
});
