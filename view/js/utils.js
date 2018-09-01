function alertBoxPopAndOut($alertBox, msg) {
    let alertType = $alertBox.attr("class").split('-')[1];
    alertType = alertType.charAt(0).toUpperCase() + alertType.slice(1);

    $alertBox.html("<strong>" + alertType + '!</strong>&nbsp;&nbsp;&nbsp;' + msg);
    $alertBox.animate((function() {
        setTimeout(() => {
            $alertBox.animate({
                top: "-100px"
            });
        }, 1700);
        return {
            top: "0px"
        }
    })());
}

function getQueryStringMap() {
    let map = new Map();
    
    let queryStrings = location.search.slice(1).split('&');

    for(let queryString of queryStrings) {
        let key  = queryString.split('=')[0];
        let val  = queryString.split('=')[1];

        map.set(key, val);
    }
    return map;
}