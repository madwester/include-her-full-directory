
var userFeed = new Instafeed({
    get: 'user',
    userId: '7875003315',
    limit: 6,
    resolution: 'standard_resolution',
    accessToken: '7875003315.1677ed0.2f8fc411def5425a91aea923bd263990',
    sortBy: 'most-recent',
    template: '<div class="instaimg"><li><a href="{{image}}" title="{{caption}}" target="_blank"><img src="{{image}}" alt="{{caption}}"><div class="likes">&hearts; {{likes}}</div></a></li></div>',
});
window.addEventListener('load', () => {
	userFeed.run();
});