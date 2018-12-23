# directoryWalker
### Sort RecursiveDirectoryIterator elements level-by-level
Sorts the entries in the current subdirectory (based on custom defined criteria) and stores the entries desired->current position pairs.
Because RecursiveDirectoryIterator is seekable this class can seek through the entries in the desired order.
Minimal memory and performance overhead

### Filtering uses include+exclude rulesets with regexp or bogus glob patterns
This is a simple matcher that can filter elements using include and exclude rulesets.
An elemnt is matched if it matches any include rule, as long as it does not match an exclude rule.

Rules are simple regexp patterns or glob like patterns that converts to regexp patterns.
This class joins up the include rules into one regexp and the exclude rules into an another regexp to improve the matching performance.