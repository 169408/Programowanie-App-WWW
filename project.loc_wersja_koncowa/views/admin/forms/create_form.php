<div class="create">
    <h1 class="heading">Panel CMS:</h1>
    <div class="create">
        <form action="<?=$_SERVER['REQUEST_URI']?>" name="CreateForm" enctype="multipart/form-data" method="post">
            <p>Name</p>
            <input type="text" name="login" placeholder="login" />
            <p>Email</p>
            <input type="email" name="email" placeholder="email" />
            <p>Password</p>
            <input type="password" name="password" placeholder="password" />
            <p>Repeat Password</p>
            <input type="password" name="repeat_password" placeholder="repeat password" required />

            <button type="submit" name="create_user" value="Create">Create</button>
        </form>
    </div>
</div>