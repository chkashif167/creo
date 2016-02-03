(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  function State(options) {
    this.overlay = this.timeout = null;
  }

  CodeMirror.defineOption("highlightSelectionMatches", false, function(cm, val, old) {
    highlightMatches(cm);
  });

  function highlightMatches(cm) {
    cm.operation(function() {
      cm.addOverlay(makeOverlay(false, 'matchhighlight'));
    });
  }

  function makeOverlay(hasBoundary, style) {
    return {token: function(stream) {
      if (stream.match(/<\?php [^\?]+ \?>/)) {
        return style;
      }
      stream.next();
    }};
  }
});