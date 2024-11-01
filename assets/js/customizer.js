( function( api ) {
    'use strict';
    wp.customize.bind('ready', function() {
        mca_customizer();
    });
}( wp.customize ) );