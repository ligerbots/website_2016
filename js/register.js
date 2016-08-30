// conveniences
var gradYear = $("[name='graduation-year']")
gradYear.attr('placeholder', new Date().getFullYear() + 4)

$('input.form-control[type=text],input.form-control[type=number]')
.on('focus', function() {
  this.select()
})

/**
 * Displays either the 'parent' or 'student' form group
 */
function setGroupActive(which) {
  $('.form-section.parent').toggleClass('active', which == 'parent')
  $('.form-section.student').toggleClass('active', which == 'student')
  $('.form-section.parent [data-required]').prop('required', which == 'parent')
  $('.form-section.student [data-required]').prop('required', which == 'student')
  location.hash = '#' + which
}

// if the back button was pressed, display what was previously selected
if(location.hash.length > 1) {
  setGroupActive(location.hash.replace('#', ''))
}

$('input[name=user-type][value=student]').on('click', function(){
  setGroupActive('student')
  $('input[name=role-parent]')
        .prop('disabled', true)
        .prop('checked', false)
        .parent().addClass('hidden')
  $('input[name=role-mentor]')
        .prop('disabled', true)
        .prop('checked', false)
        .parent().addClass('hidden')
  $('input[name=role-coach]')
        .prop('disabled', true)
        .prop('checked', false)
        .parent().addClass('hidden')
  
  $('input[name=role-exec]')
        .prop('disabled', false)
        .parent().removeClass('hidden')
  
  $('.roles-group').addClass('active')
})
$('input[name=user-type][value=adult]').on('click', function(){
  setGroupActive('none')
  $('input[name=role-exec]')
        .prop('disabled', true)
        .prop('checked', false)
        .parent().addClass('hidden')
  
  $('input[name=role-parent]')
        .prop('disabled', false)
        .parent().removeClass('hidden')
  $('input[name=role-mentor]')
        .prop('disabled', false)
                .parent().removeClass('hidden')
  $('input[name=role-coach]')
        .prop('disabled', false)
        .parent().removeClass('hidden')
  
  $('.roles-group').addClass('active')
})

$('input[name=role-parent]').on('change', function(e) {
  if ( $(e.target).prop('checked') ) setGroupActive('parent')
  else setGroupActive('none')
})

/**
 * Makes sure the last X button can't be pressed
 * (so there's at least 1 input all the time)
 */
function resetMultiInput(itemsDiv) {
  $(itemsDiv).find('.multi-input-remove')
    .prop('disabled', $(itemsDiv).children().length < 2)
}

/**
 * Adds a new input group from the template (eg, when the Add button is pressed)
 */
function appendMultiInputItem(templateHtml, itemsDiv) {
  var node = $(templateHtml)
  node.find('.multi-input-remove').on('click', function(){
    node.remove()
    resetMultiInput(itemsDiv)
  })
  itemsDiv.append(node)
  resetMultiInput(itemsDiv)
}

// set up each multi-input group
$('.multi-input').each(function(i, multiInput) {
  var templateHtml = $(multiInput).find('.multi-input-template').html()
  var items = $(multiInput).find('.multi-input-items')
  appendMultiInputItem(templateHtml, items)
  $(multiInput).find('.multi-input-add').on('click', function() {
    appendMultiInputItem(templateHtml, items)
  })
})

// auto format with hyphens
$('.phone-group').each(function(i, phoneGroup) {
  $(phoneGroup).find('input.form-control').on('paste blur', function() {
    var input = this.value;
      input = input.replace(/[^0-9]/g, "");
      var inputlen = input.length;
      
      if ( inputlen >= 9 )
      {
          input = input.replace( /^1?([0-9]{3})([0-9]{3})([0-9]{4}).*$/, "\$1-\$2-\$3" );
          $(this).val( input );
      }
      else if ( inputlen == 7 )
      {
          input = input.replace( /([0-9]{3})([0-9]{4}).*$/, "\$1-\$2" );
          $(this).val( input );
      }
      // leave alone if does not match
  })
})

$('input[name=state]').on( 'paste blur', function() {
    var input = this.value;
    input = input.toLowerCase();
    if ( /^mass(\.|achusetts)?$/.test( input ) )
        $(this).val( 'MA' );
})
    
// For number type inputs, maxlength seems to be ignored
function limit(e) {
  var max = parseInt($(this).attr('maxlength'))
  if($(this).val().length >= max) {
    $(this).val($(this).val().slice(0, max))
    e.preventDefault()
  }
}
$('input[type=number][maxlength]')
  .on('keypress', limit)
  .on('change', limit)

function validate() {
    var schoolNone = $('input[name=school][value=none]').prop('checked')
    var isStudent = $('input[name=user-type][value=student]').prop('checked')
    var isParent = $('input[name=role-parent]').prop('checked')
    if ( ( isStudent || isParent ) && schoolNone )
    {
        alert( "Students and Parents must select either North or South as a school" );
        return false;
    }
    
    var p1 = $("[name='password']").val();
    var p2 = $("[name='password-confirm']").val();
    if ( p1 != p2 ) {
        alert( 'Passwords do not match' );
        return false;
    }

    if ( ! ( /[A-Z]/.test( p1 ) && /[a-z]/.test( p1 ) && /[0-9]/.test( p1 ) ) )
    {
        alert( 'Password should contain both upper and lowercase letters and a digit' )
        return false;
    }
    
    return true;
}
