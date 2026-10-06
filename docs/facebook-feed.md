# Facebook Page feed

Shows the latest posts from the institute's Facebook Page on the **About** page, drawn as site cards. Video posts play inside their card with Facebook's video plugin; other posts link out to Facebook.

## How it works

- `App\Services\FacebookFeed::posts()` calls the Graph API for the Page's posts (`/{page-id}/posts`).
- Results are cached for **60 minutes** under the key `facebook_feed_posts`. Saving the Social settings clears the cache.
- If the Page ID or token is missing, or Facebook returns an error, the method returns an empty list and the section is hidden. Nothing breaks on the page.
- Each post is mapped to `message`, `date`, `image`, `url` and `video` (true for `/videos/` and `/reel/` links).
- Rendering lives in `resources/views/components/web/facebook-feed.blade.php`, and the About page includes it only when there are posts.
- The video plugin needs Facebook's SDK. The About page loads it only when a video post is shown.

## Settings

Admin → **Settings → Social**:

| Field | Key | Notes |
|---|---|---|
| Facebook URL | `social_facebook_url` | The Page's public link, used for the "Follow us" button. |
| Facebook Page ID | `facebook_page_id` | The numeric Page ID (see below). Not the profile ID from the URL. |
| Facebook Page Access Token | `facebook_page_token` | Stored **encrypted** with `Crypt`, like the mail password. A blank field keeps the saved token. |

Never commit the token or paste it into chat, tickets or screenshots.

## Getting the Page ID and token

The steps below assume the person doing them is an admin of the Facebook Page, and has been added as an administrator or a full-control user.

1. **Create a Meta app** at developers.facebook.com → My Apps → Create app. Choose a business type, and add the use case **Manage everything on your Page**.
2. **App settings → Basic:** fill in the privacy policy URL (`https://himalayaneducationpathways.edu.np/privacy-policy`), the terms URL, a category, a contact email and an icon. Facebook blocks logins with "Feature unavailable" until these are saved.
3. **Leave the app in development mode.** Business verification and App Review are only needed to make the app public for other users.
4. **Log into the Graph API Explorer** (developers.facebook.com/tools/explorer) with the admin's personal account, not the Page or profile account.
5. Select the app in **Meta App**. Under **User or Page**, choose **User Token**, then add the permissions `pages_show_list` and `pages_read_engagement`.
6. Click **Generate Access Token**. In the login dialog, choose **Edit settings** and tick the Page. Clicking Continue reuses the previous choices and can leave out the Page.
7. Query `me/accounts`. If the list is empty, the logged-in account isn't an admin of the Page, or the Page wasn't ticked in step 6.
8. Copy the Page's `id` (this is the **Page ID**) and its `access_token`.
9. **Extend the token:** in the Access Token Debugger, paste the user token and click **Extend Access Token**. A Page token obtained from the extended user token doesn't expire. Repeat step 7 or query `{page-id}?fields=id,access_token` with the extended token to get the long-lived Page token.
10. Enter the Page ID and the Page token in **Settings → Social** and save.

To confirm the Page ID, query `{page-id}?fields=id,name`. The response should show the institute's name.

## Troubleshooting

Errors are written to `storage/logs/laravel.log` on the server, not to the browser console. Look for lines containing "Facebook":

```
tail -n 100 storage/logs/laravel.log | grep -i facebook
```

| Log message | Meaning | Fix |
|---|---|---|
| `code 100`, "Object with ID ... does not exist" | The ID is not a Page. Usually the profile ID from the URL. | Use the Page `id` from `me/accounts`. |
| `code 190`, "Invalid OAuth 2.0 Access Token" | The token is wrong, or it's a user token, or it was made with an app that was deleted. | Generate a new Page token and save it. |
| `code 190`, "Session has expired" | A short-lived token. | Extend the user token, then get a new Page token. |
| `code 190`, "Application has been deleted" | The app the token belongs to was removed. | Create a new app and token. |
| `code 100`, "Tried accessing nonexisting field (posts)" | The ID doesn't have a posts edge. | Check the Page ID. |
| "could not be decrypted" | `APP_KEY` changed since the token was saved. | Re-enter the token in Settings → Social. |

The log never contains the token. Only the status, Facebook's error code and message are written.

## Changing the cache or the number of posts

- The feed is fetched with `FacebookFeed::posts(6)` on the About page. Change the argument to show more or fewer posts.
- To refresh now: `php artisan cache:forget facebook_feed_posts`.

## Limitations

- Facebook video playback depends on Facebook. Videos must be public, and the Page must allow embedding.
- Posts come from the Graph API, so anything Facebook hides from the API (for example, some Reels) may not appear.
- Development mode only shows content to people with a role on the app. That's fine for displaying the Page's own posts on the website.
- Permissions can change with Facebook's API versions. The code uses `v26.0`, so check the docs if the feed stops working after an update.
