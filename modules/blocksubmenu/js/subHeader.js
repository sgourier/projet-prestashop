/**
 * Created by Sylvain Gourier on 25/05/2016.
 */

$(document).ready(function ()
{
    $("#subCatMenu").slick({
        dots: false,
        autoplay: false,
        infinite: false,
        slidesToShow: Math.floor(nbItemSubMenu/2),
        slidesToScroll: Math.floor(nbItemSubMenu/2)
    });
});
