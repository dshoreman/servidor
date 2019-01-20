<template>
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal header centered">
                <i class="server icon"></i>
                Create your Servidor Account
            </h2>
            <form class="ui form" method="POST" action="/api/register">
                <div class="ui stacked segment">
                    <div class="field left aligned">
                        <label for="name">Name</label>

                        <div class="ui left icon input">
                            <input id="name" type="text" name="name" required autofocus>
                            <i class="user icon"></i>
                        </div>
                    </div>

                    <div class="field left aligned">
                        <label for="email">E-Mail Address</label>

                        <div class="ui left icon input">
                            <input id="email" type="email" name="email" required>
                            <i class="envelope icon"></i>
                        </div>
                    </div>

                    <div class="field left aligned">
                        <label for="password">Password</label>

                        <div class="ui left icon input">
                            <input id="password" type="password" name="password" required>
                            <i class="lock icon"></i>
                        </div>
                    </div>

                    <div class="field left aligned">
                        <label for="password-confirm">Confirm Password</label>

                        <div class="ui left icon input">
                            <input id="password-confirm" type="password" name="password_confirmation" required>
                            <i class="lock icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="ui teal large fluid button">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    mounted () {
        document.body.classList.add('login');
    },
    beforeRouteLeave (to, from, next) {
        document.body.classList.remove('login');

        return next();
    },
};
</script>
