/**
 * Created by Mykola Chomenko mykola.chomenko@dipcom.cz
 */
(function(){
    var enable = false;
    var modal = $('#translate-modal');
    $(document).keydown(function (event) {
        if (event.ctrlKey) {
            if (event.altKey) {
                if (!enable) {
                    enable = true;
                    console.log(1);
                    $('.translate-item').addClass('active');
                    $('[data-toggle="tooltip"]').tooltip("toggle");
                }
            }
        }
    });
    $('body').on('click', '.translate-item.active', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var name = $(this).attr('data-trans-name');
        var url = modal.attr('data-link');
        $.ajax({
            url: url,
            data: {name: name},
            success: function(data){
                var form = modal.find("form");
                form.find('.trans-name').text(name);
                form.find('input[name="name"]').val(name);
                form.find('textarea[name="translate"]').val(data.translate);
                modal.modal("show")
            }
        });
    })
    $(document).keyup(function(event) {
        if(enable) {
            $('.translate-item').removeClass('active');
            $('[data-toggle="tooltip"]').tooltip("hide");
            enable = false;
        }
    });
})();

