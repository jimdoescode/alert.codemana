var React = require("react");

module.exports = React.createClass({
    propTypes: {
        'githubAuthUrl': React.PropTypes.string,
        'githubScopes': React.PropTypes.string,
        'githubStateParam': React.PropTypes.string,
        'githubClientId': React.PropTypes.string,

        'bitbucketAuthUrl': React.PropTypes.string,
        'bitbucketClientId': React.PropTypes.string
    },

    getDefaultProps: function() {
        return {
            'githubAuthUrl': 'https://github.com/login/oauth/authorize',
            'githubScopes': 'user:email,repo'
        };
    },

    render: function() {
        return (
            <div>
                <a href={this.props.githubAuthUrl+"?client_id="+this.props.githubClientId+"&scope="+this.props.githubScopes+"&state="+this.props.githubStateParam}>
                    GitHub Login
                </a>
            </div>
        );
    }

});