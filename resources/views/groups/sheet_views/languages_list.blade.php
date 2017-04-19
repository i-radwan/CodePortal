<select name="solution_lang" id="solution_lang"
        onchange="app.editor.getSession().setMode('ace/mode/'+ this.options[this.selectedIndex].value);">
    <option value="c_cpp">C/C++</option>
    <option value="java">Java</option>
    <option value="python">Python</option>
    <option value="php">Php</option>
    <option value="javascript">Javascript</option>
    <option value="ruby">Ruby</option>
    <option value="haskell">Haskell</option>
</select>
