onChange: function(contents, editable) {
this.infoText = contents;
//console.log(contents, editable,'xxx');

var selection = document.getSelection();
var cursorPos = selection.anchorOffset;
//console.log(selection, cursorPos);
/*console.log(selection.anchorNode.textContent.substring(selection.extentOffset,selection.anchorOffset));*/

/*
var sel, word = "";
if (document.getSelection && (sel = selection).modify) {
var selectedRange = sel.getRangeAt(0);
sel.collapseToStart();
sel.modify("move", "backward", "word");
sel.modify("extend", "forward", "word");

word = sel.toString();

// Restore selection
sel.removeAllRanges();
sel.addRange(selectedRange);
} else if ( (sel = document.selection) && sel.type != "Control") {
var range = sel.createRange();
range.collapse(true);
range.expand("word");
word = range.text;
}
console.log(word);
*/


/*var caret = getCaretPosition(selection);

var result = /\S+$/.exec(selection.anchorNode.textContent.slice(0, selection.anchorNode.textContent.indexOf(' ',caret.end)));
var lastWord = result ? result[0] : null;
console.log(lastWord);*/

var stopCharacters = [' ','',String.fromCharCode(160),String.fromCharCode(NaN)," ",'<','>','/', '\n', '\r', '\t'];
/*
var text = selection.anchorNode.nodeValue;
var start = cursorPos-1;
var end = cursorPos-1;
while (start > 0)
{
if (stopCharacters.indexOf(text[start]) == -1)
--start;
else break;
}
while ((start<text.length)&&(stopCharacters.indexOf(text[start]) != -1)) start++;
//console.log('start' , start,':',text.charCodeAt(start-1),';',text.charCodeAt(start),';', text.charCodeAt(start+1),stopCharacters.indexOf(text[start]));

while (end < text.length)
{
if (stopCharacters.indexOf(text[end]) == -1)
++end;
else break;
}
while ((end>0)&&(stopCharacters.indexOf(text[end]) != -1)) end--;

if (stopCharacters.indexOf(text[cursorPos-1]) != -1) word = '';
else word = text.substr(start, end-start+1);

//console.log('end' , end,':',text[end-1],';',text[end],';', text[end+1],stopCharacters.indexOf(text[end]));
//console.log('end' , end,':',text.charCodeAt(end-1),';',text.charCodeAt(end),';', text.charCodeAt(end+1),stopCharacters.indexOf(text[start]));

//console.log(text, text.length);
//console.log(cursorPos-1, start, end, word);

console.log(word);

if (word == 'test') {
var text = selection.anchorNode.nodeValue;
console.log(selection);
//selection.anchorNode.nodeValue = text.substring(0,start-1) + text.substring(end+1, text.length);
console.log(selection);
selection.anchorOffset = 2;
selection.baseOffset = 2;
selection.extentOffset = 2;
selection.focusOffset = 2;
console.log('new text: ',selection.anchorNode.nodeValue);

//document.execCommand('insertHtml', null, '<p>My text <span style="color:red;">here</span></p>');
}*/

/*
var code = $('#addForumReplyMessageCode' + iParentId).summernote('code');
console.log(code);
//var sText = $('#addForumReplyMessageCode' + iParentId).summernote('code').replace(/<\/?[^>]+(>|$)/g, "");;
//console.log(sText);

if (code.search ('@ STUDENT')> -1) {
var string = code.split ('@ STUDENT');
string[1] = string[1].substring('@ STUDENT'.length);
code = insertTag (string, 'STUDENT');
//console.log(code);
$('#addForumReplyMessageCode' + iParentId).summernote('code',code);
}
*/

//http://summernote.org/deep-dive/#insertnode
//$('#summernote').summernote('insertImage', url, filename);
/*$('#summernote').summernote('insertImage', url, function ($image) {
$image.css('width', $image.width() / 3);
$image.attr('data-filename', 'retriever');*/
}.bind(this),