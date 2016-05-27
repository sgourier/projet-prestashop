/**
 * Created by Sylvain Gourier on 25/05/2016.
 */

$(document).ready(function ()
{
    $("#subCatMenu").slick({
        dots: false,
        autoplay: false,
        infinite: false,
        slidesToShow: nbItemSubMenu,
        slidesToScroll: nbItemSubMenu
    });
});
