:-dynamic maintitle_to_skin/2.
:-dynamic maintitle/2.

maintitle(1).
maintitle_title(1,'Demo Maintitle of Epidermis 1').
maintitle_to_skin(1,1).

:-dynamic highlight/2.
:-dynamic highlight_to_maintitle/2.

highlight(1).
highlight_title(1, 'Demo highlight text').
highlight_to_maintitle(1,1).

:-dynamic graph_to_skin/2.
:-dynamic skin_to_skin/2.
graph_to_skin(15,2).
graph_to_skin(18,2).
graph_to_skin(14,2).
graph_to_skin(16,2).
graph_to_skin(17,2).
graph_to_skin(13,2).

graph_to_skin(12,2).
graph_to_skin(24,2).
graph_to_skin(25,2).
graph_to_skin(26,2).

skin_to_skin(1,1).
graph_to_skin(38,3).
skin_to_skin(2,2).
skin_to_skin(5,4).
skin_to_skin(4,3).
skin_to_skin(1,5).
skin_to_skin(3,2).
skin_to_skin(4,2).
:-dynamic skin_to_graph/2.
skin_to_graph(2,10).
skin_to_graph(3,16).
skin_to_graph(4,18).
skin_to_graph(1,23).
skin_to_graph(3,10).
skin_to_skin(3,3).
skin_to_skin(2,3).
skin_to_skin(1,4).
:-dynamic graph_to_graph/2.
graph_to_graph(1,12).
graph_to_graph(2,12).
graph_to_graph(3,12).
skin_to_graph(1,1).
skin_to_graph(3,1).
skin_to_graph(5,2).
:-dynamic skin_to_maintitle/2.
skin_to_maintitle(1,1).
graph_to_skin(4,1).
graph_to_skin(8,1).
:-dynamic text_to_skin/2.
text_to_skin(13,1).
skin_to_skin(2,1).
skin_to_skin(2,5).
skin_to_skin(4,1).
skin_to_skin(2,4).
:-dynamic text_to_text/2.
text_to_text(14,1).
text_to_text(13,2).
text_to_skin(13,2).
text_to_skin(14,2).
text_to_skin(15,2).
:-dynamic graph_to_text/2.
graph_to_text(1,13).
graph_to_text(2,13).
graph_to_text(3,14).
graph_to_text(2,14).
graph_to_text(6,15).
text_to_skin(66,3).
text_to_skin(29,3).
text_to_skin(31,3).
graph_to_text(7,66).
graph_to_text(1,31).
graph_to_text(8,29).
:-dynamic skin_to_text/2.
skin_to_text(1,23).
skin_to_text(1,13).
skin_to_text(2,14).
skin_to_text(5,14).

:-dynamic text_hl_to_skin/3.
text_hl_to_skin(14,2,'Yet there are still no lawns on the roofs, no tree-tenants, no plant-driven water purification plants, no humus toilets, no rights to windows, no duties to the trees. The essential reafforestation of the town has not come about. ').
text_hl_to_skin(14,2,'In 1953 I realized that the straight line leads to the downfall of mankind. But the straight line has become an absolute tyranny. The straight line is something cowardly drawn with a rule, without thought or feeling; it is a line which does not exist in nature. And that line is the rotten foundation of our doomed civilization. Even if there are certain places where it is recognized that this line is rapidly leading to perdition, its course continues to be plotted. The straight line is atheistic and immoral.').
text_hl_to_skin(13,1,'for three months I have been back in my paradise, where the trees, which I planted, have already become forests in which one can walk. I am working on four paintings, which testify even more intensely to my vegetative way of painting.').
text_hl_to_skin(15,2,'Thank you for your letter. I have received so many letters with the same request by this time that I have written down a standard reply. I don&rsquo;t have that here, so I&rsquo;ll answer again.').
text_hl_to_skin(13,1,'It is this, which distinguishes me from other painters. ').
text_hl_to_skin(13,4,'for three months I have been back in my paradise, where the trees, which I planted, have already become forests in which one can walk. I am working on four paintings, which testify even more intensely to my vegetative way of painting.').
:-dynamic text_hl_to_maintitle/3.
text_hl_to_maintitle(13,1,'I do not paint expressive or imitative as has become fashionable today ').
text_hl_to_skin(15,4,'A number of widely divergent aspects are involved here.').
text_hl_to_skin(13,1,'It is this, which distinguishes me from other painters.').
text_hl_to_skin(13,1,'Cemeteries will turn into forests of life. Today, however, burial is a sacrilege against ecology, against life, against the circular flow, against rebirth,').
text_hl_to_skin(14,2,'An ecologist without a conscience is doomed to failure, and the same is true of an artist who does not bow to the laws of nature.').
text_hl_to_skin(15,2,'Thank you for your letter. I have received so many letters with the same request by this time that I have written down a standard reply. ').
text_hl_to_skin(15,2,'Thank you for your letter.').
text_hl_to_skin(15,2,'I hope you have no objections if I use this letter as a standard reply to other requests of this kind, such as, &ldquo;How can I, with my normal income, acquire a Hundertwasser original?&rdquo;').
text_hl_to_skin(15,2,'Dear friend,').
text_hl_to_skin(31,3,'Some of the postcards in my collection are very worn from being looked at so much. Some are hard to get. Wherever I am, in Paris, Venice, Vienna, America, my first stop is a good bookshop, to add to my collection. In Oslo, for instance, I discovered painters I had never heard of, on postcards! J. C. Dahl and Harald Sohlberg, for example. ').
text_hl_to_skin(31,4,'A different aspect: does one want a painting for its intrinsic or its financial value?').
text_hl_to_skin(31,4,'I am sending you, dear friend, a few postcards from my collection which I like very much and have duplicates of. I can&rsquo;t part with my favourites.').
text_hl_to_skin(31,5,'A third aspect: it was always my goal to have as many people derive pleasure from my art as possible.').
text_hl_to_skin(15,2,'hank you for your letter. I have received so many letters with the same request by this time that I have written down a standard reply.').
text_hl_to_skin(13,1,'for three months I have been back in my paradise, where the trees, which I planted, have already become forests').
text_hl_to_skin(31,3,'Hundertwasser changed his first name to Friedereich while he was living in Japan for a year in 1961 ').
text_hl_to_skin(13,2,'for three months I have been back in my paradise, where the trees, which I planted, have already become forests in which one can walk. I am working on four paintings, which testify even more intensely to my vegetative way of painting.').
text_hl_to_skin(14,5,'An ecologist without a conscience is doomed to failure, and the same is true of an artist who does not bow to the laws of nature.').
text_hl_to_skin(13,5,'forests in which one can walk.').
text_hl_to_skin(13,5,'for three months I have been back in my paradise, where the trees,').
text_hl_to_skin(13,5,'which I planted, have already become forests in which one can walk. I am working on four paintings').
text_hl_to_skin(13,5,'which testify even more intensely to my vegetative way of painting').
text_hl_to_skin(13,1,'I do not paint expressive or imitative as has become fashionable today &mdash; still fashionable, but vegetative &mdash; a').
text_hl_to_skin(13,1,'For now, one judges my paintings according to their optical appearance as though they were expressive-imitative, and not according their actual content').
text_hl_to_skin(13,1,'One of the most important points regarding the peace treaty ').
text_hl_to_skin(13,1,'signpost ').
text_hl_to_skin(13,1,'with nature is the reunion').
text_hl_to_skin(13,1,'he creation of man').
text_hl_to_skin(13,1,'creation of nature').
text_hl_to_skin(13,1,'creatively, organically,').
text_hl_to_skin(13,1,'cheer, because I know ').
text_hl_to_skin(13,1,'xpressive-imitative, and not').
text_hl_to_skin(14,2,'In 1953 I realized that the straight line leads to the downfall of mankind. ').
text_hl_to_skin(14,2,'I spoke of columns of grey men on the march toward sterility and self-destruction.').
text_hl_to_skin(14,2,'The same year I used the term &raquo;transautomation&laquo; to show the way').
text_hl_to_skin(14,2,'Even if there are certain places where it is recognized that this line is rapidly leading to perdition, its course continues to be plotted. The straight line is atheistic and immoral.').
text_hl_to_skin(14,2,'The straight line is the only sterile line, the only line which does not suit man as the image of God.').
text_hl_to_skin(15,2,'Thank you for your letter. I have received so many letters with the same request by this time that I have written down a standard reply.').
text_hl_to_skin(15,2,'Our true illiteracy is our inability to be creative').
text_hl_to_skin(13,1,'A piece of territory which man has illegally occupied is given back to nature.').
text_hl_to_skin(13,1,'humus, giving new life a foundation.
Such a cemetery is ').
text_hl_to_skin(14,2,'The world has not improved. The dangers felt have turned into reality.').
text_hl_to_skin(15,2,'Also, pictures by children, framed, for example, can be head and shoulders above any Van Gogh. You just have to look around a bit.').
text_hl_to_skin(15,2,'Even in newspapers you can often find masterpieces!').
text_hl_to_skin(13,1,'one can walk. I am working on four paintings, which testify even more intensely to my vegetative way of painting.

It is this, which distinguishes me from other painters. I do not paint expressive ').
