!function($){"use strict";$.fn.select2.locales["pt-BR"]={formatNoMatches:function(){return"Nenhum resultado encontrado"},formatInputTooShort:function(e,t){var n=t-e.length;return"Digite mais "+n+" caracter"+(1==n?"":"es")},formatInputTooLong:function(e,t){var n=e.length-t;return"Apague "+n+" caracter"+(1==n?"":"es")},formatSelectionTooBig:function(e){return"Só é possível selecionar "+e+" elemento"+(1==e?"":"s")},formatLoadMore:function(e){return"Carregando mais resultados…"},formatSearching:function(){return"Buscando…"}},$.extend($.fn.select2.defaults,$.fn.select2.locales["pt-BR"])}(jQuery);