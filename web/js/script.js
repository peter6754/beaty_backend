$(document).ready(function () {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                xhr.setRequestHeader("X-CSRF-Token", $('meta[name=csrf-token]').attr('content'));
            }
        }
    });

    $(".phone_mask").mask("+7(999) 999-99-99");

    $('#navbarDropdown').dropdown()
    $('#navbarDropdown2').dropdown()

    var token = "b23f3bf9d575835285d2a495cf924f9ede199c9d";

    var type = "ADDRESS";
    var $city = $("#orderapplication-city");
    var $street = $("#orderapplication-street");
    var $house = $("#orderapplication-house");

    // город и населенный пункт
    $city.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "city-settlement",
    });

    // улица
    $street.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        constraints: $city,
        count: 15
    });

    // дом
    $house.suggestions({
        token: token,
        type: type,
        hint: false,
        noSuggestionsHint: false,
        bounds: "house",
        constraints: $street,
        onSelect: function (suggestion) {
            $("#orderapplication-lat").val(suggestion.data.geo_lat);
            $("#orderapplication-lon").val(suggestion.data.geo_lon);
        }
    });

    var $city2 = $("#masterproceedform-work_city");
    var $street2 = $("#masterproceedform-work_street");
    var $house2 = $("#masterproceedform-work_house");

    // город и населенный пункт
    $city2.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "city-settlement",
    });

    // улица
    $street2.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        constraints: $city2,
        count: 15
    });

    // дом
    $house2.suggestions({
        token: token,
        type: type,
        hint: false,
        noSuggestionsHint: false,
        bounds: "house",
        constraints: $street2,
        onSelect: function (suggestion) {
            $("#masterproceedform-work_lat").val(suggestion.data.geo_lat);
            $("#masterproceedform-work_lon").val(suggestion.data.geo_lon);
        }
    });

    var $city3 = $("#masterproceedform-live_city");
    var $street3 = $("#masterproceedform-live_street");
    var $house3 = $("#masterproceedform-live_house");

    // город и населенный пункт
    $city3.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "city-settlement",
    });

    // улица
    $street3.suggestions({
        token: token,
        type: type,
        hint: false,
        bounds: "street",
        constraints: $city3,
        count: 15
    });

    // дом
    $house3.suggestions({
        token: token,
        type: type,
        hint: false,
        noSuggestionsHint: false,
        bounds: "house",
        constraints: $street3,
        onSelect: function (suggestion) {
            $("#masterproceedform-live_lat").val(suggestion.data.geo_lat);
            $("#masterproceedform-live_lon").val(suggestion.data.geo_lon);
        }
    });

    $(document).on('click', '.btn-locate', function (e) {
        e.preventDefault();
        navigator.geolocation.getCurrentPosition(function (position) {

            // Текущие координаты.
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/geolocate/address";
            var token = "b23f3bf9d575835285d2a495cf924f9ede199c9d";
            var query = { lat: lat, lon: lng };

            var options = {
                method: "POST",
                mode: "cors",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Token " + token
                },
                body: JSON.stringify(query)
            }

            fetch(url, options)
                .then(response => response.text())
                .then(result => {
                    result = JSON.parse(result)
                    if (result.suggestions.length > 0) {
                        let item = result.suggestions[0]
                        $("#orderapplication-city").val(item.data.city_with_type)
                        $("#orderapplication-street").val(item.data.street_with_type)
                        $("#orderapplication-house").val(item.data.house)
                        $("#orderapplication-lat").val(lat);
                        $("#orderapplication-lon").val(lng);
                    }
                })
                .catch(error => console.log("error", error));

        });

    });

    $(document).on('click', '#add_coupon', function (e) {
        e.preventDefault();
        $("#orderapplication-order_coupon_id").val($(this).attr("data-coupon_id"))
        document.getElementById("total_price").innerHTML = $(this).attr("data-price")
        $(this).hide();
    });

    $(document).on('click', '#buy_coupon', function (e) {
        e.preventDefault();
        $('#coupon-modal').modal('show')
        $("#couponform-coupon_id").val($(this).attr("data-coupon_id"))
    });

    $("#masters-address").suggestions({
        token: "b23f3bf9d575835285d2a495cf924f9ede199c9d",
        type: "ADDRESS",
        hint: false,
        restrict_value: true,
        onSelect: function (suggestion) {
            $("#masters-lat").val(suggestion.data.geo_lat);
            $("#masters-lon").val(suggestion.data.geo_lon);
        }
    });

});

function loadCoupon(id) {
    $.get("/api/order/coupon?id=" + id, function (data) {
        $(".coupon_block").html(data);
    })
}