!function($){"use strict";$.fn.select2.locales.de={formatNoMatches:function(){return"Keine Übereinstimmungen gefunden"},formatInputTooShort:function(e,n){return"Bitte "+(n-e.length)+" Zeichen mehr eingeben"},formatInputTooLong:function(e,n){return"Bitte "+(e.length-n)+" Zeichen weniger eingeben"},formatSelectionTooBig:function(e){return"Sie können nur "+e+" Eintr"+(1===e?"ag":"äge")+" auswählen"},formatLoadMore:function(e){return"Lade mehr Ergebnisse…"},formatSearching:function(){return"Suche…"},formatMatches:function(e){return e+" Ergebnis "+(e>1?"se":"")+" verfügbar, zum Navigieren die Hoch-/Runter-Pfeiltasten verwenden."}},$.extend($.fn.select2.defaults,$.fn.select2.locales.de)}(jQuery);