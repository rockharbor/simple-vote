# Vote app

This is a ridiculously simple voting app. It doesn't require any sort of login,
and uses cookies to remember if someone voted. It's not designed to be fool
proof, just simple and quick yes or no questions.

## Install

Grab the code using your method of choice, download, clone, or submodule. 
Now create the default config and default empty database:

```
$ mv database.sqlite3.default database.sqlite3
$ mv config.json.default config.json
```

## Configuration

### Database

If you want to use a different database, change the database name in the config
JSON file to point to the SQLite database.

### Refresh interval

The counter page refreshes every 5 seconds by default. You can disable this by 
changing the `refresh` config key to `false` to not refresh, or an integer.

## Administration

There are no administrative scripts to creating polls and questions. It's all
done via the `sqlite3` CLI.

### Create a poll

```
$ sqlite3 database.sqlite3
sqlite> insert into polls (title, expires, enabled, slug) values
   ...> ("New Poll", null, 1, "new-poll");
```

#### Fields

- `title`: The poll title, shown when the user is taking the poll.
- `expires`: A `DATETIME` when the poll expires. If null, it never expires.
- `enabled`: Boolean (1|0). If 0, users cannot participate in the poll.
- `slug`: A URL-friendly name for the poll

### Create some questions

First, get the rowid of the poll you want to add questions to:

```
$ sqlite3 database.sqlite3
sqlite> select rowid, * from polls;
1|New Poll||1|new-poll
```

Then insert some questions:

```
$ sqlite3 database.sqlite3
sqlite> insert into questions (question, `order`, votes, poll_id) VALUES
   ...> ("Do you like the color green?", 1, 0, 1),
   ...> ("Is this a question?", 2, 0, 1),
   ...> ("Do you like pie?", 3, 0, 1);
```

#### Fields

- `question`: The question.
- `order`: The order of the question (among its peers).
- `votes`: Number of 'yes' votes.
- `poll_id`: The poll this question belongs to.

## Taking a poll

To take a poll, visit `/poll/<slug>` where `<slug>` is the slug you entered when
creating the poll.

## Viewing totals

To take a poll, visit `/counter/<slug>` where `<slug>` is the slug you entered 
when creating the poll. The total refreshes itself every 5 seconds.

## License

Copyright (c) 2012 *ROCK*HARBOR

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.