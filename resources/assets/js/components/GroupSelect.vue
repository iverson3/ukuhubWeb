<template>
    <div>
        <div class="card-scene">
            <Container
                orientation="horizontal"
                @drop="onColumnDrop($event)"
                drag-handle-selector=".column-drag-handle"
                @drag-start="dragStart">

                <Draggable v-for="column in scene.children" :key="column.id">
                    <div :class="column.props.className" class="drop-container-div">
                        <div class="card-column-header">
                            <span class="column-drag-handle">&#x2630;</span>
                            {{column.name}}
                        </div>
                        <Container
                            group-name="col"
                            @drop="(e) => onCardDrop(column.id, e)"
                            @drag-start="(e) => log('drag start', e)"
                            @drag-end="(e) => log('drag end', e)"
                            :get-child-payload="getCardPayload(column.id)"
                            drag-class="card-ghost"
                            drop-class="card-ghost-drop">

                            <Draggable v-for="card in column.children" :key="card.id">
                                <div :class="card.props.className" :style="card.props.style" class="drop-div">
                                    <div v-html="card.data" class="member-info">
                                        {{card.data}}
                                    </div>
                                </div>
                            </Draggable>
                        </Container>
                    </div>
                </Draggable>
            </Container>
        </div>
        <a class="btn btn-default" href="javascript:;" onclick="window.history.back()">返回</a>
        <button class="btn btn-primary" @click="saveSetting">保存</button> <span>{{saveTip}}</span>
        <div class="export-div">
            <button class="btn btn-default" @click="showExport">显示导出面板</button>
            <div v-show="isShowExport">
                <template v-for="(field,index) in fieldList">
                    <input type="checkbox" v-model='fields' :value="field.field"> {{field.title}}
                </template>
                <br/><button class="btn btn-primary" @click="exportGroup">导出</button>
            </div>
        </div>
    </div>
</template>

<script>
  import { Container, Draggable } from "vue-smooth-dnd";
  import { applyDrag, generateItems, generateChildItems } from "../tools/utils";

  export default {
    name: "GroupSelect",
    props: ['isEdit', 'fetch_api_url', 'save_api_url', 'export_api_url', 'activity_id', 'uid'],
    components: { Container, Draggable },
    data () {
      return {
        groups: [],
        saveTip: '',
        isShowExport: false,
        fieldList: [
          {field: 'name', 'title': '名字'},
          {field: 'wechat', 'title': '微信号'},
          {field: 'music_type', 'title': '乐器类型'},
          {field: 'level', 'title': '能力分类'},
          {field: 'remark', 'title': '备注信息'}
        ],
        fields: [],

        scene: null
      }
    },
    mounted() {
      this.$http.post(this.fetch_api_url, {activity_id: this.activity_id, uid: this.uid})
        .then(function (response) {
          this.groups = response.data.groups
          console.log(this.groups)

          this.scene = {
            type: "container",
            props: {
              orientation: "horizontal"
            },
            children: generateItems(this.groups, (i, key, childList) => ({
              id: `column${i}`,
              type: "container",
              name: key,
              props: {
                orientation: "vertical",
                className: "card-container"
              },
              children: generateChildItems(childList, (child) => {
                let color = "khaki"
                if (child.join_status == 0) {
                  color = "#FFDC35"
                }
                if (child.status == 0) {
                  color = "red"
                }
                return {
                  type: "draggable",
                  id: `${child.id}`,
                  props: {
                    className: "card",
                    style: { backgroundColor: color }
                  },
                  data: `<p style="max-width:200px;">${child.name} (${child.wechat})</p> <p>${child.music_type} - ${child.level}</p> <p style="max-width:180px;line-height:15px;">备注：${child.remark}</p>`
                }
              })
            }))
          };
        })
        .then(function (error) {
          console.log(error)
        });

    },
    methods: {
      onColumnDrop: function (dropResult) {
        const scene = Object.assign({}, this.scene);
        scene.children = applyDrag(scene.children, dropResult);
        this.scene = scene;
      },
      onCardDrop: function (columnId, dropResult) {
        if (dropResult.removedIndex !== null || dropResult.addedIndex !== null) {
          const scene = Object.assign({}, this.scene);
          const column = scene.children.filter(p => p.id === columnId)[0];
          const columnIndex = scene.children.indexOf(column);
          const newColumn = Object.assign({}, column);
          newColumn.children = applyDrag(newColumn.children, dropResult);
          scene.children.splice(columnIndex, 1, newColumn);
          this.scene = scene;
          console.log(this.scene)
        }
      },
      getCardPayload: function (columnId) {
        return index => {
          return this.scene.children.filter(p => p.id === columnId)[0].children[
            index
          ];
        };
      },
      dragStart: function () {
        console.log('drag started');
      },
      log: function (...params) {
        console.log(...params);
      },

      saveSetting () {
        this.saveTip = '设置中...'
        let data = {}
        this.scene.children.forEach((group) => {
          let name = group.name
          if (name !== '未分组') {
            data[name] = group.children.map((member) => {
              return parseInt(member.id)
            })
          }
        })

        Object.keys(data).map((key) => {
          if (data[key].length === 0) {
            this.saveTip = '不允许组长没有组员,请重新设置'
          }
        })
        if (this.saveTip === '不允许组长没有组员,请重新设置') {
          return false
        }
        console.log(data)

        this.$http.post(
          this.save_api_url,
          {'activity_id': this.activity_id, 'groups': data},
          {emulateJSON: true})
          .then(res => res.json())
          .then(function (response) {
            if (response.success) {
              this.saveTip = '设置成功'
            } else {
              this.saveTip = '设置失败'
            }
            console.log(response)
          })
          .catch(err => {
            this.saveTip = '设置出错'
            console.log(err)
          })
      },

      showExport () {
        this.isShowExport = !this.isShowExport
      },
      exportGroup () {
        if (this.fields.length === 0) {
          alert('请先选择需要导出的字段')
          return false
        }
        let fields = this.fields.join(',')

        let url = this.export_api_url + '?activity_id=' + this.activity_id + '&fields=' + fields
        window.location.href = url

      }
    }
  }
</script>

<style scoped>
    .drop-container-div {
        margin-right: 20px;
    }
    .card-column-header {
        margin-bottom: 10px;
    }
    .drop-div {
        /*padding: 20px 10px 20px 10px;*/
        /*margin: 20px 0px 10px 0px;*/
    }
    .member-info {
        line-height: 12px;
    }
    .export-div {
        margin-top: 20px;
    }
</style>