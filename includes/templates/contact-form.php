<form id="enquiry_form">
    <label>Name</label><br />
    <input type="text" name="name"><br /><br />

    <label>Email</label><br />
    <input type="text" name="email"><br /><br />

    <label>Phone</label><br />
    <input type="text" name="phone"><br /><br />

    <label>Your message</label><br />
    <textarea name="message"></textarea><br /><br />

    <button type="submit">Submit form</button>
</form>

<script>
    jQuery(document).ready(function($){

        $(enquiry_form).submit( function(event){
            alert('test');
        })
    });
</script>