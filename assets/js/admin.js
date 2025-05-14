jQuery(document).ready(function($) {
    // Initiera datumväljare
    $('.film-datumväljare').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10'
    });
    
    // Initiera tidväljare
    $('.tidväljare').timepicker({
        timeFormat: 'HH:mm',
        interval: 30,
        minTime: '10:00',
        maxTime: '23:30',
        defaultTime: '19:00',
        startTime: '10:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
    
    // Lägg till visningstid
    $('.lägg-till-visning').on('click', function() {
        var container = $(this).siblings('.visningstider-container');
        var index = container.children().length;
        
        var html = `
            <div class="visningstid-post">
                <input type="text" name="film_visningstider[${index}][datum]" class="visning-datum film-datumväljare" placeholder="Datum">
                <input type="text" name="film_visningstider[${index}][tid]" class="visning-tid tidväljare" placeholder="HH:MM">
                <input type="text" name="film_visningstider[${index}][språk]" class="visning-språk" placeholder="Språk">
                <button type="button" class="button ta-bort-visning">Ta bort</button>
            </div>
        `;
        
        container.append(html);
        
        // Initiera nya väljare
        container.find('.film-datumväljare').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:+10'
        });
        
        container.find('.tidväljare').timepicker({
            timeFormat: 'HH:mm',
            interval: 30,
            minTime: '10:00',
            maxTime: '23:30',
            defaultTime: '19:00',
            startTime: '10:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
    });
    
    // Ta bort visningstid
    $('.visningstider-container').on('click', '.ta-bort-visning', function() {
        $(this).parent().remove();
    });
});