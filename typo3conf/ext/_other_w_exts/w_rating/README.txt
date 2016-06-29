
* NOTE: No jQuery provided! Please include it yourself.



* Options reference:

plugin.tx_wrating_pi1	{

	table_name - this can be any table from database

	record_uid - uid of record in that table which is voted

	record_uid.special - for now available value is "current", which with table set to "pages" sets voting to current page (TSFE->id)

	mode - value can be for now: "full", which means votable and text info about rating, or "stars" - only show non-interactive current rating

	starsNumber - integer, number of stars / max vote

	templateCode - template is simple, so imho there's no need to keep it in external file. can be set here. note, that "votable" and "message" are needed.
}
