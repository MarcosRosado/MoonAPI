(function ($) {
    'use strict';
    /*==================================================================
        [ Daterangepicker ]*/
    try {
        $('.js-datepicker').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "autoUpdateInput": false,
            locale: {
                format: 'DD/MM/YYYY'
            },
        });
    
        var myCalendar = $('.js-datepicker');
        var isClick = 0;
    
        $(window).on('click',function(){
            isClick = 0;
        });
    
        $(myCalendar).on('apply.daterangepicker',function(ev, picker){
            isClick = 0;
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
    
        });
    
        $('.js-btn-calendar').on('click',function(e){
            e.stopPropagation();
    
            if(isClick === 1) isClick = 0;
            else if(isClick === 0) isClick = 1;
    
            if (isClick === 1) {
                myCalendar.focus();
            }
        });
    
        $(myCalendar).on('click',function(e){
            e.stopPropagation();
            isClick = 1;
        });
    
        $('.daterangepicker').on('click',function(e){
            e.stopPropagation();
        });
    
    
    } catch(er) {console.log(er);}
    /*[ Select 2 Config ]
        ===========================================================*/
    
    try {
        var selectSimple = $('.js-select-simple');
    
        selectSimple.each(function () {
            var that = $(this);
            var selectBox = that.find('select');
            var selectDropdown = that.find('.select-dropdown');
            selectBox.select2({
                dropdownParent: selectDropdown
            });
        });
    
    } catch (err) {
        console.log(err);
    }


    $('.validate-form').on('submit',function(){
        var array = $('.validate-form').serializeArray();
        if (array[3]['value'] !== array[2]['value']){
            md.showNotification('top', 'center', 'As senhas não conferem', 2);
        }
        else{
            $.ajax({
                url:"../private/page_cadastro/cadastro.php",
                type:"post",
                data:{"email":array[1]['value'],"senha":array[2]['value'],"nome":array[0]['value']},
                success: function(result){
                    const response = jQuery.parseJSON(result);
                    if(response["response"] == 200 ){
                        window.location.href = "../LoginPage/index.html";
                    }
                    else{
                        md.showNotification('top', 'center', 'Email já cadastrado', 2);
                    }
                }

            })
        }


        return false;

    });


    

})(jQuery);