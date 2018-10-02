/**
 * getToken obtains a token key from an azapp webservice that will be used to 
 * call another web services, namely the one called in the function getNews
 *
 *
 * @author João Neto - WorldIT
 */
function getToken()	{

	const tokenEndpoint =  'https://azapp-services.azurewebsites.net/api/security/token';

	const tokenHeaders = {
    	"Content-Type": "application/x-www-form-urlencoded"
  	};

	const tokenData = 'grant_type=password&username=volta18tvOS&password=volta18#tvOS';

	const tokenOptions = {
    "method": "post", 
    "headers": tokenHeaders, 
    "body": tokenData
  };

  return fetch(tokenEndpoint, tokenOptions)
	.then(response => response.json());

};

/*
	getNews(): to obtain JSON with the news from the Endpoint, using GET method,
	to be displayed in the webpage
 */
 
/**
 * getNews receives the desired feed data from azapp web services.
 * it uses the token obtained in getToken, the desired category, and 
 * the number of results to be shown.
 * 
 * <p>Bugs: dateISO its still fixed value. Still not decided what should be.
 *
 * @author João Neto - WorldIT
 */
 function getNews(token, category, results)	{

	let key = token['access_token'];
	let keyType = token['token_type'];
	
	//const date = '2018-09-12T15:48:30.743Z';

	var date = new Date();
    //var dateISO = date.toISOString();
	var dateISO = '2018-08-11T15:48:30.743Z';

	const newsData = 'page=0&records='+results+'&date='+dateISO+'&id=5&lang=PT&theme='+category;

	const newsEndpoint =  'https://azapp-services.azurewebsites.net/api/Info/GetUpdatesAndDeletes/?'+newsData;

	const newsHeaders = {
    	"Authorization": "Bearer " + key,
    	"Content-Type": "application/x-www-form-urlencoded"
  	};

	const newsOptions = {
    	method: "get", 
    	headers: newsHeaders
 	};

	return fetch(newsEndpoint, newsOptions)
		.then(newsResponse => newsResponse.json());
	
};

/**
* setFeed receives data from web service containing news updates.
* It sets the html to be inserted inside table1 in index.html
*
*
* @author João Neto - WorldIT
*/
function setFeed(updates)	{

	var html = '';
	
	updates.forEach(function (entry)	{
		
		var link = entry['LINK'];
		var image;
		var images_many = entry['Images_many'];
		var title = entry['TITLE'];
		var content = entry['HTML_EDITOR'];		
			
		
		html += '<tr>';
		html += '<td rowspan="2"><a href="'+link+'"><div>';
		
		
		images_many.forEach(function (image)	{

			html += '<div><img src="'+image['IMAGE_URL']+'" alt="'+image['IMAGE_NAME']+'" /></div><br>';
			
		});
		
		html += '</div></a></td>';
		html += '<td class="tg-0pky"><a href="'+link+'">'+title+'</a></td></tr>';
		html += '<tr><td><span>'+content+'</span></td></tr>';
		
	});

	document.getElementById("table1").innerHTML = html;
	
};

/**
 * getFeed returns the desired feed from azapp web services. It receives 
 * the desired category of the feed, and the number of results per page.
 * First it calls getToken, in order to obtain the token that will allow to call 
 * the webservice to obtain the feed through getNews. The received data is then sent 
 * to setFeed, so that the table1 in index.html gets populated.
 *
 * <p>Bugs: (a list of bugs and other problems)
 *
 * @author (your name)
 */
function getFeed(categories, resultsPerPage)
{
	//Get token and news feed
	getToken()
		.then(token => {

			//console.log(token['access_token']);
		
			//Send token and chosen category to Endpoint in order to retrieve feed
			getNews(token, categories, resultsPerPage)
				.then(news => {

					//console.log(news);
					
					const updates = news['updates'];

					//console.log(news['updates'][0]['HTML_EDITOR']);
				
					//update table1
					setFeed(updates);

				});

			});
}

jQuery(document).ready(function() {
	
	var font_families = Feed.font_families;
	
	WebFont.load({
		google: {
			families: font_families
        }
    });
				
	var category 	= Feed.category;
	var results 	= Feed.results;

	getFeed(category, results);
	
});