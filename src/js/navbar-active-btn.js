var checktoggle = false;
$(document).ready(function () {
    activateMenu();

});

/*
 * This function toggles the nav menu active/inactive status as
 * different pages are selected. It should be called from $(document).ready()
 * or whenever the page loads.
 */
function activateMenu()
{
    var current_page_URL = location.href;
    console.log(current_page_URL);
    $(".navbar-nav a").each(function (){
        var target_URL = $(this).prop("href");
        if (target_URL === current_page_URL){
            $('nav a').parents('li, ul').removeClass('active');
            $(this).parent('li').addClass('active');
            return false;
        }
    });
}