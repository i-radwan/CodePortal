<!-- Comments Form -->
<div class="well">
    <h4>Leave a Comment:</h4>
    <form role="form" action="blogs/add_comment" method="POST">
        {{csrf_field()}}
        <div class="form-group">
            <textarea class="form-control" rows="3" ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<hr>
