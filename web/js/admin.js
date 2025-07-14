$(document).ready(function() {
    $(".phone_mask").mask("+7(999) 999-99-99");

    $("#masters-address").suggestions({
        token: "b23f3bf9d575835285d2a495cf924f9ede199c9d",
        type: "ADDRESS",
        hint: false,
        restrict_value: true,
        onSelect: function(suggestion) {
            $("#masters-lat").val(suggestion.data.geo_lat);
            $("#masters-lon").val(suggestion.data.geo_lon);
        }
    });

});