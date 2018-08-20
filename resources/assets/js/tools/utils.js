export const applyDrag = (arr, dragResult) => {
  const { removedIndex, addedIndex, payload } = dragResult
  if (removedIndex === null && addedIndex === null) return arr

  const result = [...arr]
  let itemToAdd = payload

  if (removedIndex !== null) {
    itemToAdd = result.splice(removedIndex, 1)[0]
  }

  if (addedIndex !== null) {
    result.splice(addedIndex, 0, itemToAdd)
  }

  return result
};

export const generateItems = (groups, creator) => {
  const result = []
  let i = 0;
  Object.keys(groups).forEach((key) => {
    result.push(creator(i, key, groups[key]))
    i++
  })
  return result
};

export const generateChildItems = (members, creator) => {
  const result = []
  Object.keys(members).forEach((key) => {
    result.push(creator(members[key]))
  })
  return result
};